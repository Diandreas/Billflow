<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Supplier;
use App\Models\Brand;

class ImportMapperController extends Controller
{
    /**
     * Afficher le formulaire d'importation
     */
    public function showImportForm()
    {
        // Vérifier les permissions
        if (Gate::denies('manage-products') && Gate::denies('admin')) {
            abort(403, 'Action non autorisée.');
        }

        return view('products.import-form', [
            'categories' => ProductCategory::orderBy('name')->get(),
            'suppliers' => Supplier::orderBy('name')->get(),
            'brands' => Brand::orderBy('name')->get()
        ]);
    }

    /**
     * Analyser le fichier importé et afficher l'interface de mappage
     */
    public function analyzeFile(Request $request)
    {
        // Vérifier les permissions
        if (Gate::denies('manage-products') && Gate::denies('admin')) {
            abort(403, 'Action non autorisée.');
        }

        // Valider la requête
        $validated = $request->validate([
            'product_file' => 'required|file|mimes:csv,txt,xls,xlsx',
            'has_headers' => 'nullable|boolean',
            'category_id' => 'nullable|exists:product_categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'brand_id' => 'nullable|exists:brands,id',
            'create_missing_brands' => 'nullable|boolean',
        ]);

        $file = $request->file('product_file');
        $hasHeaders = isset($validated['has_headers']) && $validated['has_headers'];
        $defaultCategoryId = $validated['category_id'] ?? null;
        $defaultSupplierId = $validated['supplier_id'] ?? null;
        $defaultBrandId = $validated['brand_id'] ?? null;
        $createMissingBrands = isset($validated['create_missing_brands']) && $validated['create_missing_brands'];

        try {
            // Lire le fichier
            $spreadsheet = IOFactory::load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Si le fichier est vide
            if (empty($rows)) {
                return back()->with('error', 'Le fichier est vide.');
            }

            // Extraire les en-têtes (première ligne)
            $headers = $hasHeaders ? array_shift($rows) : [];

            // Préparer les données pour la vue de mappage
            $sampleData = array_slice($rows, 0, 5); // Prendre les 5 premières lignes pour l'aperçu
            
            // Si pas d'en-têtes, générer des en-têtes génériques
            if (!$hasHeaders) {
                $headers = [];
                for ($i = 0; $i < count($rows[0] ?? []); $i++) {
                    $headers[] = 'Colonne ' . ($i + 1);
                }
            }

            // Obtenir la liste des champs attendus pour les produits
            $expectedFields = [
                'name' => 'Nom du produit',
                'description' => 'Description',
                'default_price' => 'Prix de vente',
                'type' => 'Type (physical/service)',
                'sku' => 'Référence/SKU',
                'stock_quantity' => 'Quantité en stock',
                'stock_alert_threshold' => 'Seuil d\'alerte',
                'accounting_category' => 'Catégorie comptable',
                'tax_category' => 'Catégorie fiscale',
                'cost_price' => 'Prix d\'achat',
                'status' => 'Statut (actif/inactif)',
                'category' => 'Catégorie',
                'supplier' => 'Fournisseur',
                'brand' => 'Marque',
                'model' => 'Modèle',
                'is_barterable' => 'Disponible pour troc (0/1)'
            ];

            // Faire une suggestion de mappage automatique
            $suggestedMapping = $this->suggestColumnMapping($headers, $expectedFields);

            // Stocker les données dans la session pour le traitement ultérieur
            session([
                'import_data' => [
                    'rows' => $rows,
                    'headers' => $headers,
                    'has_headers' => $hasHeaders,
                    'category_id' => $defaultCategoryId,
                    'supplier_id' => $defaultSupplierId,
                    'file_name' => $file->getClientOriginalName()
                ]
            ]);

            // Rediriger vers la page de remappage
            return view('products.import-mapping', compact(
                'headers', 
                'sampleData', 
                'expectedFields',
                'suggestedMapping',
                'defaultCategoryId',
                'defaultSupplierId',
                'defaultBrandId',
                'createMissingBrands'
            ));
        } catch (ReaderException $e) {
            Log::error('Erreur lors de la lecture du fichier: ' . $e->getMessage());
            return back()->with('error', 'Impossible de lire le fichier. Vérifiez que le format est correct.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'importation: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue: ' . $e->getMessage());
        }
    }

    /**
     * Suggérer un mappage automatique des colonnes
     */
    private function suggestColumnMapping($headers, $expectedFields)
    {
        $suggestedMapping = [];
        foreach ($headers as $index => $header) {
            $bestMatch = null;
            $bestScore = 0;
            
            foreach ($expectedFields as $field => $label) {
                // Normaliser pour la comparaison
                $normalizedHeader = $this->normalizeString($header);
                $normalizedField = $this->normalizeString($field);
                $normalizedLabel = $this->normalizeString($label);
                
                // Calculer la similarité avec le nom du champ
                $fieldScore = similar_text($normalizedHeader, $normalizedField, $fieldPercent);
                
                // Calculer la similarité avec le libellé
                $labelScore = similar_text($normalizedHeader, $normalizedLabel, $labelPercent);
                
                // Prendre le meilleur score entre les deux
                $score = max($fieldPercent, $labelPercent);
                
                if ($score > $bestScore && $score > 60) { // Seuil de 60% de similarité
                    $bestScore = $score;
                    $bestMatch = $field;
                }
            }
            
            $suggestedMapping[$index] = $bestMatch;
        }
        
        return $suggestedMapping;
    }

    /**
     * Normaliser une chaîne pour comparaison
     */
    private function normalizeString($string)
    {
        // Convertir en minuscules et supprimer les accents
        $string = strtolower($string);
        $string = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $string);
        
        // Supprimer les caractères spéciaux et les espaces superflus
        $string = preg_replace('/[^a-z0-9]/', ' ', $string);
        $string = preg_replace('/\s+/', ' ', $string);
        $string = trim($string);
        
        return $string;
    }

    /**
     * Traiter les données remappées de l'importation
     */
    public function processMapping(Request $request)
    {
        // Vérifier les permissions
        if (Gate::denies('manage-products') && Gate::denies('admin')) {
            abort(403, 'Action non autorisée.');
        }

        // Valider la requête
        $validated = $request->validate([
            'column_mapping' => 'required|array',
            'column_mapping.*' => 'nullable|string',
            'category_id' => 'nullable|exists:product_categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
        ]);

        // Récupérer les données de session
        $importData = session('import_data');
        if (!$importData) {
            return redirect()->route('products.import.form')
                ->with('error', 'Les données d\'importation ont expiré. Veuillez recommencer.');
        }

        $rows = $importData['rows'];
        $headers = $importData['headers'];
        $defaultCategoryId = $validated['category_id'] ?? $importData['category_id'];
        $defaultSupplierId = $validated['supplier_id'] ?? $importData['supplier_id'];

        // Créer le mapping des colonnes
        $columnMap = [];
        foreach ($validated['column_mapping'] as $index => $field) {
            if (!empty($field)) {
                $columnMap[$field] = (int)$index;
            }
        }

        // Valider que les champs obligatoires sont mappés
        if (!isset($columnMap['name'])) {
            return back()->with('error', 'Le champ "Nom du produit" doit être mappé.');
        }

        try {
            DB::beginTransaction();

            $productsCreated = 0;
            $productsUpdated = 0;
            $errors = [];

            foreach ($rows as $rowIndex => $row) {
                try {
                    // Extraire les données du produit en utilisant le mapping des colonnes
                    $productData = $this->extractProductData($row, $columnMap, $defaultCategoryId, $defaultSupplierId);

                    // Essayer de trouver un produit existant par SKU s'il est défini
                    $existingProduct = null;
                    if (!empty($productData['sku'])) {
                        $existingProduct = Product::where('sku', $productData['sku'])->first();
                    }

                    if ($existingProduct) {
                        // Mettre à jour le produit existant
                        $existingProduct->update($productData);
                        $productsUpdated++;
                    } else {
                        // Créer un nouveau produit
                        Product::create($productData);
                        $productsCreated++;
                    }
                } catch (\Exception $e) {
                    // Enregistrer l'erreur et continuer avec la ligne suivante
                    $rowNumber = $rowIndex + ($importData['has_headers'] ? 2 : 1);
                    $errors[] = "Ligne {$rowNumber}: " . $e->getMessage();
                }
            }

            DB::commit();

            // Message de succès avec détails
            $message = "Importation réussie: {$productsCreated} produits créés, {$productsUpdated} produits mis à jour.";
            if (!empty($errors)) {
                $message .= " Il y a eu " . count($errors) . " erreurs.";
            }

            return redirect()->route('products.index')
                ->with('success', $message)
                ->with('import_errors', $errors);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors du traitement de l\'importation: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue: ' . $e->getMessage());
        }
    }

    /**
     * Extraction des données produit avec mapping personnalisé
     */
    private function extractProductData(array $row, array $columnMap, $defaultCategoryId = null, $defaultSupplierId = null)
    {
        $data = [];

        // Champs principaux du produit
        $fields = [
            'name', 'description', 'default_price', 'type', 'sku', 
            'stock_quantity', 'stock_alert_threshold', 'accounting_category',
            'tax_category', 'cost_price', 'status', 'is_barterable'
        ];

        foreach ($fields as $field) {
            if (isset($columnMap[$field]) && isset($row[$columnMap[$field]])) {
                $value = trim($row[$columnMap[$field]]);
                if ($value !== '') {
                    // Conversion de type selon le champ
                    switch ($field) {
                        case 'default_price':
                        case 'cost_price':
                            $data[$field] = (float) str_replace(',', '.', $value);
                            break;
                        case 'stock_quantity':
                        case 'stock_alert_threshold':
                            $data[$field] = (int) $value;
                            break;
                        case 'is_barterable':
                            $data[$field] = in_array(strtolower($value), ['1', 'true', 'oui', 'yes']);
                            break;
                        case 'type':
                            $data[$field] = strtolower($value) === 'service' ? 'service' : 'physical';
                            break;
                        case 'status':
                            $data[$field] = strtolower($value) === 'inactif' ? 'inactif' : 'actif';
                            break;
                        default:
                            $data[$field] = $value;
                    }
                }
            }
        }

        // S'assurer que le champ name est présent
        if (empty($data['name'])) {
            throw new \Exception('Le nom du produit est obligatoire.');
        }

        // Valeurs par défaut si non spécifiées
        if (!isset($data['type'])) {
            $data['type'] = 'physical';
        }
        if (!isset($data['status'])) {
            $data['status'] = 'actif';
        }
        if (!isset($data['default_price'])) {
            $data['default_price'] = 0;
        }

        // Si c'est un service, définir les quantités à zéro
        if ($data['type'] === 'service') {
            $data['stock_quantity'] = 0;
            $data['stock_alert_threshold'] = 0;
            $data['is_barterable'] = false;
        }

        // Gestion de la catégorie (soit par ID direct, soit par nom à rechercher)
        if (isset($columnMap['category']) && isset($row[$columnMap['category']])) {
            $categoryName = trim($row[$columnMap['category']]);
            if (!empty($categoryName)) {
                $category = ProductCategory::firstOrCreate(['name' => $categoryName]);
                $data['category_id'] = $category->id;
            } elseif ($defaultCategoryId) {
                $data['category_id'] = $defaultCategoryId;
            }
        } elseif ($defaultCategoryId) {
            $data['category_id'] = $defaultCategoryId;
        }

        // Gestion du fournisseur (soit par ID direct, soit par nom à rechercher)
        if (isset($columnMap['supplier']) && isset($row[$columnMap['supplier']])) {
            $supplierName = trim($row[$columnMap['supplier']]);
            if (!empty($supplierName)) {
                $supplier = Supplier::firstOrCreate(['name' => $supplierName]);
                $data['supplier_id'] = $supplier->id;
            } elseif ($defaultSupplierId) {
                $data['supplier_id'] = $defaultSupplierId;
            }
        } elseif ($defaultSupplierId) {
            $data['supplier_id'] = $defaultSupplierId;
        }

        return $data;
    }
}

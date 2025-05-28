<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Barter;
use App\Models\BarterItem;
use App\Models\Bill;
use App\Models\Brand;
use App\Models\ProductModel;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::withCount('bills')
            ->withSum('bills as total_sales', DB::raw('bill_items.quantity * bill_items.price'))
            ->select('products.*'); // S'assurer que toutes les colonnes sont chargées pour les méthodes isLowStock et isOutOfStock

        // Filtrer les produits par boutique pour les non-administrateurs
        if (!Gate::allows('admin')) {
            $shopIds = Auth::user()->shops->pluck('id')->toArray();

            // Trouver les produits qui ont des mouvements d'inventaire dans ces boutiques
//            $query->whereHas('inventoryMovements', function($q) use ($shopIds) {
//                $q->whereIn('shop_id', $shopIds);
//            });
        }

        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filtre par type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filtre par marque
        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        // Filtre par modèle
        if ($request->filled('product_model_id')) {
            $query->where('product_model_id', $request->product_model_id);
        }

        // Filtre par boutique spécifique
        if ($request->filled('shop_id')) {
            $shopId = $request->shop_id;
            $query->whereHas('inventoryMovements', function($q) use ($shopId) {
                $q->where('shop_id', $shopId);
            });
        }

        // Filtre par état de stock (seulement pour les produits physiques)
        if ($request->filled('stock')) {
            switch ($request->stock) {
                case 'available':
                    $query->where('type', '!=', 'service')
                        ->where('stock_quantity', '>', 0);
                    break;
                case 'low':
                    $query->where('type', '!=', 'service')
                        ->whereColumn('stock_quantity', '<=', 'stock_alert_threshold')
                        ->where('stock_alert_threshold', '>', 0);
                    break;
                case 'out':
                    $query->where('type', '!=', 'service')
                        ->where('stock_quantity', '<=', 0);
                    break;
            }
        }

        // Tri
        $sortField = $request->input('sort', 'name');
        $sortDirection = $request->input('direction', 'asc');

        if ($sortField === 'stock') {
            $query->orderBy('stock_quantity', $sortDirection);
        } elseif ($sortField === 'price') {
            $query->orderBy('default_price', $sortDirection);
        } else {
            $query->orderBy($sortField, $sortDirection);
        }

        $products = $query->paginate(15)->withQueryString();

        // Récupérer les boutiques pour le filtre
        $shops = Gate::allows('admin')
            ? \App\Models\Shop::all()
            : Auth::user()->shops;
            
        // Récupérer les marques pour le filtre
        $brands = Brand::orderBy('name')->get();
        
        // Récupérer les modèles si une marque est sélectionnée
        $models = collect();
        if ($request->filled('brand_id')) {
            $models = ProductModel::where('brand_id', $request->brand_id)
                ->orderBy('name')
                ->get();
        }

        // Préparer les statistiques pour la vue
        $stats = [
            'total_products' => Product::count(),
            'active_products' => Product::where('status', 'actif')->count(),
            'physical_products' => Product::where('type', 'physical')->count(),
            'total_revenue' => Product::withSum('bills as total_sales', DB::raw('bill_items.quantity * bill_items.price'))->sum('total_sales'),
            'average_price' => Product::avg('default_price') ?: 0,
        ];

        return view('products.index', compact('products', 'shops', 'stats', 'brands', 'models'));
    }

    public function create()
    {
        // Vérifier que l'utilisateur a le droit de gérer les produits
        if (Gate::denies('manage-products') && auth()->user()->role !== 'vendeur') {
            abort(403, 'Action non autorisée.');
        }

        return view('products.create');
    }

    public function store(Request $request)
    {
        // Vérifier que l'utilisateur a le droit de gérer les produits
        if (Gate::denies('manage-products') && auth()->user()->role !== 'vendeur') {
            abort(403, 'Action non autorisée.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'default_price' => 'nullable|numeric|min:0',
            'type' => 'nullable|string|max:50',
            'sku' => 'nullable|string|max:50',
            'stock_quantity' => 'nullable|integer|min:0',
            'stock_alert_threshold' => 'nullable|integer|min:0',
            'accounting_category' => 'nullable|string|max:50',
            'tax_category' => 'nullable|string|max:50',
            'cost_price' => 'nullable|numeric|min:0',
            'status' => 'nullable|string|in:actif,inactif',
            'category_id' => 'nullable|exists:product_categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'brand_id' => 'nullable|exists:brands,id',
            'product_model_id' => 'nullable|exists:product_models,id',
            'is_barterable' => 'nullable|boolean',
        ]);

        // S'assurer que le prix par défaut n'est jamais NULL
        if (!isset($validated['default_price']) || $validated['default_price'] === null) {
            $validated['default_price'] = 0;
        }

        // Si le type n'est pas spécifié, considérer comme un service par défaut
        if (empty($validated['type'])) {
            $validated['type'] = 'service';
        }

        // Pour les services, mettre les valeurs de stock à zéro et désactiver le troc
        if ($validated['type'] === 'service') {
            $validated['stock_quantity'] = 0;
            $validated['stock_alert_threshold'] = 0;
            $validated['is_barterable'] = false;
        }

        // Conversion de la valeur de is_barterable
        $validated['is_barterable'] = isset($validated['is_barterable']) && $validated['is_barterable'] ? true : false;

        $product = Product::create($validated);

        // Enregistrer l'activité de création de produit
        ActivityLogger::logCreated($product, "Produit {$product->name} créé par " . Auth::user()?->name);

        if ($request->ajax() || $request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'product' => $product,
                'message' => 'Produit créé avec succès'
            ]);
        }

        return redirect()
            ->route('products.show', $product)
            ->with('success', 'Produit créé avec succès');
    }

    public function show(Product $product)
    {
        // Récupérer tous les IDs de produits pour la navigation
        $productIds = Product::orderBy('name')->pluck('id')->toArray();
        $currentIndex = array_search($product->id, $productIds) + 1; // +1 pour l'affichage (1-based)
        $totalProducts = count($productIds);

        // Déterminer les produits précédent et suivant
        $previousIndex = ($currentIndex > 1) ? $currentIndex - 2 : $totalProducts - 1;
        $nextIndex = ($currentIndex < $totalProducts) ? $currentIndex : 0;

        $previousProduct = $productIds[$previousIndex];
        $nextProduct = $productIds[$nextIndex];
        $firstProduct = $productIds[0];
        $lastProduct = $productIds[$totalProducts - 1];

        // Statistiques
        $stats = $this->getProductStats($product);

        // Historique des prix directement calculé
        $priceHistory = $this->getPriceHistory($product);

        // Récupérer les factures associées à ce produit
        $invoices = Bill::whereHas('items', function($query) use ($product) {
            $query->where('product_id', $product->id);
        })
            ->with(['client', 'items' => function($query) use ($product) {
                $query->where('product_id', $product->id);
            }])
            ->orderBy('date', 'desc')
            ->get();

        // Ajouter les informations de pivot
        foreach ($invoices as $invoice) {
            $item = $invoice->items->first();
            if ($item) {
                $invoice->pivot = (object)[
                    'price' => $item->unit_price,
                    'quantity' => $item->quantity,
                    'total' => $item->total
                ];
            }
        }

        // Si c'est un produit physique, récupérer les trocs
        $barterItems = collect();
        $barterStats = [
            'total_barters' => 0,
            'total_quantity' => 0,
            'total_value' => 0,
            'average_value' => 0,
            'given_barters' => 0,
            'received_barters' => 0
        ];

        if ($product->type === 'physical' && $product->is_barterable) {
            // Récupérer les trocs
            $barterItems = BarterItem::where('product_id', $product->id)
                ->with(['barter', 'barter.client'])
                ->orderBy('created_at', 'desc')
                ->get();

            // Calculer les statistiques des trocs
            $barterStats = $this->getBarterStats($product);
        }

        return view('products.show', compact(
            'product',
            'stats',
            'priceHistory',
            'invoices',
            'barterItems',
            'barterStats',
            'previousProduct',
            'nextProduct',
            'firstProduct',
            'lastProduct',
            'currentIndex',
            'totalProducts'
        ));
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        // Vérifier que l'utilisateur a le droit de gérer les produits
        if (Gate::denies('manage-products') && auth()->user()->role !== 'vendeur') {
            abort(403, 'Action non autorisée.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'default_price' => 'nullable|numeric|min:0',
            'type' => 'required|string|max:50',
            'sku' => 'nullable|string|max:50',
            'stock_quantity' => 'nullable|integer|min:0',
            'stock_alert_threshold' => 'nullable|integer|min:0',
            'accounting_category' => 'nullable|string|max:50',
            'tax_category' => 'nullable|string|max:50',
            'cost_price' => 'nullable|numeric|min:0',
            'status' => 'nullable|string|in:actif,inactif',
            'category_id' => 'nullable|exists:product_categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'brand_id' => 'nullable|exists:brands,id',
            'product_model_id' => 'nullable|exists:product_models,id',
            'is_barterable' => 'nullable|boolean',
        ]);

        // S'assurer que le prix par défaut n'est jamais NULL
        if (!isset($validated['default_price']) || $validated['default_price'] === null) {
            $validated['default_price'] = 0;
        }

        // Pour les services, mettre les valeurs de stock à zéro et désactiver le troc
        if ($validated['type'] === 'service') {
            $validated['stock_quantity'] = 0;
            $validated['stock_alert_threshold'] = 0;
            $validated['is_barterable'] = false;
        }

        // Conversion de la valeur de is_barterable
        $validated['is_barterable'] = isset($validated['is_barterable']) && $validated['is_barterable'] ? true : false;

        // Sauvegarder les valeurs originales pour l'historique
        $oldValues = $product->getOriginal();

        $product->update($validated);

        // Enregistrer l'activité de mise à jour
        ActivityLogger::logUpdated($product, $oldValues, "Produit {$product->name} modifié par " . Auth::user()?->name);

        return redirect()
            ->route('products.show', $product)
            ->with('success', 'Produit mis à jour avec succès');
    }

    public function destroy(Product $product)
    {
        // Vérifier si le produit est utilisé dans des factures
        if ($product->bills()->exists()) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de supprimer un produit utilisé dans des factures'
                ], 422);
            }
            return back()->with('error', 'Impossible de supprimer un produit utilisé dans des factures');
        }

        // Enregistrer l'activité de suppression
        ActivityLogger::logDeleted($product, "Produit {$product->name} supprimé par " . Auth::user()?->name);

        $product->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Produit supprimé avec succès'
            ]);
        }

        return redirect()
            ->route('products.index')
            ->with('success', 'Produit supprimé avec succès');
    }

    // API Endpoints pour les requêtes AJAX
    public function search(Request $request)
    {
        $query = $request->get('q');
        $products = Product::where('name', 'like', "%{$query}%")
            ->withCount('bills')
            ->limit(10)
            ->get()
            ->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'description' => $product->description,
                    'usage_count' => $product->bills_count
                ];
            });

        return response()->json($products);
    }

    public function quickCreate(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'default_price' => 'nullable|numeric|min:0',
        ]);

        // S'assurer que le prix par défaut n'est jamais NULL
        if (!isset($validated['default_price']) || $validated['default_price'] === null) {
            $validated['default_price'] = 0;
        }

        // Définir un type par défaut
        $validated['type'] = 'service';

        $product = Product::create($validated);

        // Enregistrer l'activité de création rapide
        ActivityLogger::logCreated($product, "Produit {$product->name} créé rapidement par " . Auth::user()?->name);

        return response()->json([
            'success' => true,
            'product' => $product,
            'message' => 'Produit créé avec succès'
        ]);
    }

    /**
     * Récupère les statistiques du produit
     */
    private function getProductStats($product) {
        // Récupérer les factures
        $items = DB::table('bill_items')
            ->join('bills', 'bill_items.bill_id', '=', 'bills.id')
            ->where('product_id', $product->id)
            ->select('bill_items.total', 'bill_items.quantity', 'bills.date')
            ->get();

        // Calculer les statistiques
        $stats = [
            'total_sales' => $items->sum('total'),
            'total_quantity' => $items->sum('quantity'),
            'average_price' => $items->count() > 0 ? $items->sum('total') / $items->sum('quantity') : 0,
            'usage_count' => $items->count(),
            'first_use' => $items->min('date') ? new \Carbon\Carbon($items->min('date')) : null,
            'last_use' => $items->max('date') ? new \Carbon\Carbon($items->max('date')) : null,
        ];

        return $stats;
    }

    /**
     * Récupère les statistiques des trocs pour un produit
     */
    private function getBarterStats($product)
    {
        // Récupérer tous les trocs associés à ce produit
        $barterItems = BarterItem::where('product_id', $product->id)->get();

        // Nombre de trocs où ce produit a été donné par le client
        $givenBarters = $barterItems->where('type', 'given')->count();

        // Nombre de trocs où ce produit a été reçu par le client
        $receivedBarters = $barterItems->where('type', 'received')->count();

        // Valeur totale des trocs impliquant ce produit
        $totalValue = $barterItems->sum('total_value');

        // Quantité totale échangée
        $totalQuantity = $barterItems->sum('quantity');

        return [
            'total_barters' => $barterItems->count(),
            'total_quantity' => $totalQuantity,
            'total_value' => $totalValue,
            'average_value' => $barterItems->count() > 0 ? $totalValue / $barterItems->count() : 0,
            'given_barters' => $givenBarters,
            'received_barters' => $receivedBarters
        ];
    }

    /**
     * Display the import form
     */
    public function showImportForm()
    {
        return view('products.import');
    }

    /**
     * Process the imported file
     */
    public function import(Request $request)
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
        ]);

        $file = $request->file('product_file');
        $hasHeaders = isset($validated['has_headers']) && $validated['has_headers'];
        $defaultCategoryId = $validated['category_id'] ?? null;
        $defaultSupplierId = $validated['supplier_id'] ?? null;

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
                'defaultSupplierId'
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
            'brand_id' => 'nullable|exists:brands,id',
            'create_missing_brands' => 'nullable|boolean',
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
        $defaultBrandId = $validated['brand_id'] ?? null;
        $createMissingBrands = $validated['create_missing_brands'] ?? true;

        // Créer le mapping des colonnes
        $columnMap = [];
        foreach ($validated['column_mapping'] as $index => $field) {
            if (!empty($field)) {
                $columnMap[$field] = $index;
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
            $brandsCreated = 0;
            $modelsCreated = 0;
            $errors = [];

            foreach ($rows as $rowIndex => $row) {
                try {
                    // Extraire les données du produit en utilisant le mapping des colonnes
                    $productData = $this->extractProductDataWithMapping($row, $columnMap, $defaultCategoryId, $defaultSupplierId);

                    // Gérer la marque si elle est présente ou utiliser la marque par défaut
                    if (!isset($productData['brand_id']) && $defaultBrandId) {
                        $productData['brand_id'] = $defaultBrandId;
                    }

                    // Si la création automatique des marques est activée et qu'une marque est spécifiée mais n'existe pas
                    if ($createMissingBrands && isset($columnMap['brand']) && !empty($row[$columnMap['brand']]) && !isset($productData['brand_id'])) {
                        $brandName = trim($row[$columnMap['brand']]);
                        $brand = Brand::firstOrCreate(['name' => $brandName]);
                        if ($brand->wasRecentlyCreated) {
                            $brandsCreated++;
                        }
                        $productData['brand_id'] = $brand->id;

                        // Gérer le modèle si une marque est trouvée/créée et un modèle est spécifié
                        if (isset($columnMap['model']) && !empty($row[$columnMap['model']])) {
                            $modelName = trim($row[$columnMap['model']]);
                            $model = ProductModel::firstOrCreate(
                                ['name' => $modelName, 'brand_id' => $brand->id],
                                ['description' => '']
                            );
                            if ($model->wasRecentlyCreated) {
                                $modelsCreated++;
                            }
                            $productData['product_model_id'] = $model->id;
                        }
                    }

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
            if ($brandsCreated > 0) {
                $message .= " {$brandsCreated} marques créées.";
            }
            if ($modelsCreated > 0) {
                $message .= " {$modelsCreated} modèles créés.";
            }
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
    private function extractProductDataWithMapping(array $row, array $columnMap, $defaultCategoryId = null, $defaultSupplierId = null)
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
                $supplier = \App\Models\Supplier::firstOrCreate(['name' => $supplierName]);
                $data['supplier_id'] = $supplier->id;
            } elseif ($defaultSupplierId) {
                $data['supplier_id'] = $defaultSupplierId;
            }
        } elseif ($defaultSupplierId) {
            $data['supplier_id'] = $defaultSupplierId;
        }
        
        // Gestion de la marque
        if (isset($columnMap['brand']) && isset($row[$columnMap['brand']])) {
            $brandName = trim($row[$columnMap['brand']]);
            if (!empty($brandName)) {
                $brand = Brand::firstOrCreate(['name' => $brandName]);
                $data['brand_id'] = $brand->id;
                
                // Gestion du modèle (uniquement si la marque est définie)
                if (isset($columnMap['model']) && isset($row[$columnMap['model']])) {
                    $modelName = trim($row[$columnMap['model']]);
                    if (!empty($modelName)) {
                        $model = ProductModel::firstOrCreate(
                            ['name' => $modelName, 'brand_id' => $brand->id],
                            ['description' => '']
                        );
                        $data['product_model_id'] = $model->id;
                    }
                }
            }
        }

        return $data;
    }

    /**
     * Download a template file for importing products
     */
    public function downloadTemplate()
    {
        $headers = [
            'Name', 'Description', 'Default Price', 'Type', 'SKU',
            'Stock Quantity', 'Stock Alert Threshold', 'Accounting Category',
            'Tax Category', 'Cost Price', 'Status', 'Category', 'Supplier', 
            'Brand', 'Model', 'Is Barterable'
        ];

        $filename = 'product_import_template.csv';
        $handle = fopen('php://temp', 'w+');

        // Add headers
        fputcsv($handle, $headers);

        // Add example row
        fputcsv($handle, [
            'Example Product',
            'This is an example product description',
            '19.99',
            'physical', // or 'service'
            'PROD-001',
            '10',
            '5',
            'PRODUCTS',
            'STANDARD',
            '12.50',
            'actif', // or 'inactif'
            'Default Category',
            'Default Supplier',
            'Samsung', // Brand example
            'Galaxy S21', // Model example
            'No'
        ]);

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return response($content)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename={$filename}");
    }

    /**
     * Export products to CSV
     */
    public function export(Request $request)
    {
        // Build query based on filters
        $query = Product::query();

        // Apply filters if provided
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('stock')) {
            switch ($request->stock) {
                case 'available':
                    $query->where('type', '!=', 'service')
                        ->where('stock_quantity', '>', 0);
                    break;
                case 'low':
                    $query->where('type', '!=', 'service')
                        ->whereColumn('stock_quantity', '<=', 'stock_alert_threshold')
                        ->where('stock_alert_threshold', '>', 0);
                    break;
                case 'out':
                    $query->where('type', '!=', 'service')
                        ->where('stock_quantity', '<=', 0);
                    break;
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Get products for export with related data
        $products = $query->with(['category', 'supplier', 'brand', 'productModel'])->get();

        // Set filename with timestamp
        $filename = 'products_export_' . date('Y-m-d_His') . '.csv';

        // Create CSV
        $handle = fopen('php://temp', 'w+');

        // CSV Headers
        $headers = [
            'ID',
            'Nom',
            'Description',
            'Prix par défaut',
            'Type',
            'SKU',
            'Quantité en stock',
            'Seuil d\'alerte',
            'Catégorie comptable',
            'Catégorie fiscale',
            'Catégorie',
            'Fournisseur',
            'Marque',
            'Modèle',
            'Prix d\'achat',
            'Statut',
            'Peut être troqué',
            'Date de création',
            'Dernière mise à jour'
        ];

        // Add UTF-8 BOM to fix accents in Excel
        fputs($handle, "\xEF\xBB\xBF");

        // Write headers
        fputcsv($handle, $headers);

        // Write data rows
        foreach ($products as $product) {
            $row = [
                $product->id,
                $product->name,
                $product->description,
                $product->default_price,
                $product->type,
                $product->sku,
                $product->stock_quantity,
                $product->stock_alert_threshold,
                $product->accounting_category,
                $product->tax_category,
                $product->category ? $product->category->name : '',
                $product->supplier ? $product->supplier->name : '',
                $product->brand ? $product->brand->name : '',
                $product->productModel ? $product->productModel->name : '',
                $product->cost_price,
                $product->status,
                $product->is_barterable ? 'Oui' : 'Non',
                $product->created_at->format('d/m/Y H:i'),
                $product->updated_at->format('d/m/Y H:i')
            ];

            fputcsv($handle, $row);
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        // Return CSV file as download
        return response($content)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename={$filename}");
    }

    /**
     * Show the export form for advanced filtering
     */
    public function showExportForm()
    {
        return view('products.export');
    }

    /**
     * Show the form for reviewing potential duplicates
     */
    public function reviewImport()
    {
        if (!session('import_duplicates')) {
            return redirect()->route('products.index')
                ->with('error', 'Aucune donnée d\'importation à vérifier.');
        }

        $duplicates = session('import_duplicates');
        $filePath = session('import_file_path');
        $columnMap = session('import_column_map');
        $importMode = session('import_mode');
        $defaultCategoryId = session('import_default_category_id');
        $defaultSupplierId = session('import_default_supplier_id');

        return view('products.review-import', compact(
            'duplicates',
            'filePath',
            'columnMap',
            'importMode',
            'defaultCategoryId',
            'defaultSupplierId'
        ));
    }

    /**
     * Process the reviewed duplicates
     */
    public function processReviewedImport(Request $request)
    {
        // Validate the data
        $validated = $request->validate([
            'duplicates' => 'required|array',
            'duplicates.*.action' => 'required|in:create,update,skip',
            'duplicates.*.product_id' => 'required_if:duplicates.*.action,update',
            'duplicates.*.row_index' => 'required|integer',
        ]);

        // Get session data
        $filePath = session('import_file_path');
        $columnMap = session('import_column_map');
        $importMode = session('import_mode');
        $defaultCategoryId = session('import_default_category_id');
        $defaultSupplierId = session('import_default_supplier_id');

        if (!$filePath || !$columnMap) {
            return redirect()->route('products.import')
                ->with('error', 'Les données de session ont expiré. Veuillez réimporter votre fichier.');
        }

        try {
            // Load the file again
            $extension = pathinfo($filePath, PATHINFO_EXTENSION);

            if ($extension == 'csv') {
                $reader = IOFactory::createReader('Csv');
                $reader->setDelimiter(',');
                $reader->setEnclosure('"');
                $reader->setSheetIndex(0);
            } else {
                $reader = IOFactory::createReader($extension == 'xlsx' ? 'Xlsx' : 'Xls');
            }

            $spreadsheet = $reader->load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();
            array_shift($rows); // Remove header row

            // Process each decision
            $results = [
                'created' => 0,
                'updated' => 0,
                'skipped' => 0,
                'errors' => []
            ];

            DB::beginTransaction();
            try {
                foreach ($validated['duplicates'] as $decision) {
                    $rowIndex = $decision['row_index'] - 2; // Adjust back to 0-based index

                    // Skip if row index is out of bounds
                    if (!isset($rows[$rowIndex])) {
                        $results['errors'][] = "Ligne {$decision['row_index']} non trouvée dans le fichier";
                        continue;
                    }

                    $row = $rows[$rowIndex];
                    $productData = $this->extractProductDataWithMapping($row, $columnMap, $defaultCategoryId, $defaultSupplierId);

                    // Skip empty rows
                    if (empty($productData['name'])) {
                        continue;
                    }

                    switch ($decision['action']) {
                        case 'create':
                            Product::create($productData);
                            $results['created']++;
                            break;

                        case 'update':
                            $product = Product::find($decision['product_id']);
                            if ($product) {
                                $product->update($productData);
                                $results['updated']++;
                            } else {
                                $results['errors'][] = "Produit ID {$decision['product_id']} non trouvé";
                            }
                            break;

                        case 'skip':
                            $results['skipped']++;
                            break;
                    }
                }

                DB::commit();

                // Clear session data
                session()->forget([
                    'import_duplicates',
                    'import_file_path',
                    'import_column_map',
                    'import_mode',
                    'import_default_category_id',
                    'import_default_supplier_id'
                ]);

                return redirect()->route('products.index')
                    ->with('success', "Import finalisé : {$results['created']} créés, {$results['updated']} mis à jour, {$results['skipped']} ignorés");

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("Error processing reviewed import: " . $e->getMessage(), [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]);

                return redirect()->back()
                    ->withErrors(['error' => 'Une erreur est survenue durant le traitement : ' . $e->getMessage()]);
            }

        } catch (\Exception $e) {
            Log::error("Error loading file for reviewed import: " . $e->getMessage());
            return redirect()->back()
                ->withErrors(['error' => 'Erreur lors du chargement du fichier : ' . $e->getMessage()]);
        }
    }

    /**
     * Récupère l'historique des prix directement depuis la base de données
     */
    private function getPriceHistory($product)
    {
        // Récupérer tous les prix utilisés pour ce produit
        $priceHistory = DB::table('bill_items')
            ->select(
                'bill_items.unit_price as price',
                DB::raw('COUNT(*) as usage_count'),
                DB::raw('SUM(bill_items.quantity) as total_quantity'),
                DB::raw('SUM(bill_items.total) as total_amount'),
                DB::raw('MIN(bills.date) as first_used'),
                DB::raw('MAX(bills.date) as last_used')
            )
            ->join('bills', 'bill_items.bill_id', '=', 'bills.id')
            ->where('product_id', $product->id)
            ->groupBy('bill_items.unit_price')
            ->orderBy('usage_count', 'desc')
            ->get();

        // Marquer le prix par défaut
        foreach ($priceHistory as $price) {
            $price->is_default = ($price->price == $product->default_price);
        }

        return $priceHistory;
    }

    /**
     * Recherche de marques pour l'autocomplétion
     */
    public function searchBrands(Request $request)
    {
        $query = $request->input('query');
        $brands = Brand::where('name', 'like', "%{$query}%")
            ->orderBy('name')
            ->limit(10)
            ->get();

        return response()->json($brands);
    }

    /**
     * Recherche de modèles pour l'autocomplétion, filtré par marque
     */
    public function searchModels(Request $request)
    {
        $query = $request->input('query');
        $brandId = $request->input('brand_id');
        
        $models = ProductModel::where('name', 'like', "%{$query}%");
        
        if ($brandId) {
            $models->where('brand_id', $brandId);
        }
        
        $results = $models->orderBy('name')
            ->limit(10)
            ->get();

        return response()->json($results);
    }

    /**
     * Création rapide d'une marque si elle n'existe pas
     */
    public function createBrand(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:brands',
            'description' => 'nullable|string',
        ]);

        $brand = Brand::create($validated);

        return response()->json([
            'success' => true,
            'brand' => $brand,
            'message' => 'Marque créée avec succès'
        ]);
    }

    /**
     * Création rapide d'un modèle si il n'existe pas
     */
    public function createModel(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'brand_id' => 'required|exists:brands,id',
            'description' => 'nullable|string',
            'year' => 'nullable|string|max:4',
            'technical_specs' => 'nullable|string',
        ]);

        // Vérifier que ce modèle n'existe pas déjà pour cette marque
        $existingModel = ProductModel::where('name', $validated['name'])
            ->where('brand_id', $validated['brand_id'])
            ->first();

        if ($existingModel) {
            return response()->json([
                'success' => false,
                'message' => 'Ce modèle existe déjà pour cette marque',
                'model' => $existingModel
            ], 422);
        }

        $model = ProductModel::create($validated);

        return response()->json([
            'success' => true,
            'model' => $model,
            'message' => 'Modèle créé avec succès'
        ]);
    }
}

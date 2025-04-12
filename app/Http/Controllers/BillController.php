<?php
namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Client;
use App\Models\Product;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\InventoryMovement;
use Illuminate\Support\Facades\Log;

class BillController extends Controller
{
    public function index(Request $request)
    {
        // Récupération des clients pour le filtre
        $clients = Client::orderBy('name')->get();

        // Requête de base pour les factures
        $query = Bill::with(['client', 'products'])->latest();

        // Application des filtres
        if ($request->filled('client')) {
            $query->where('client_id', $request->client);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('period')) {
            $query->when($request->period, function ($q, $period) {
                return match($period) {
                    'current_month' => $q->whereMonth('date', now()->month),
                    'last_month' => $q->whereMonth('date', now()->subMonth()->month),
                    'current_year' => $q->whereYear('date', now()->year),
                    default => $q
                };
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                    ->orWhereHas('client', function($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        }

        if ($request->filled('min_amount')) {
            $query->where('total', '>=', $request->min_amount);
        }

        if ($request->filled('max_amount')) {
            $query->where('total', '<=', $request->max_amount);
        }

        // Récupération des factures paginées
        $bills = $query->paginate(10);
        
        // Statistiques pour la période filtrée
        $stats = [
            'count' => $query->count(),
            'total' => $query->sum('total'),
            'average' => $query->avg('total'),
        ];

        return view('bills.index', compact('bills', 'clients', 'stats'));
    }

    public function create()
    {
        $clients = Client::orderBy('name')->get();
        $products = Product::orderBy('name')->get();
        return view('bills.create', compact('clients', 'products'));
    }

    public function store(Request $request)
    {
        try {
            // Validation des données avec des messages d'erreur personnalisés
            $validated = $request->validate([
                'client_id' => 'required|exists:clients,id',
                'date' => 'required|date',
                'tax_rate' => 'required|numeric',
                'notes' => 'nullable|string',
                'reference' => 'nullable|string|max:255',
                'products' => 'required|array|min:1',
                'quantities' => 'required|array|min:1',
                'prices' => 'required|array|min:1',
                'unit_prices' => 'array', // Assure la compatibilité avec les deux noms de champs possibles
                'status' => 'nullable|string|in:pending,paid,overdue',
                'due_date' => 'nullable|date|after_or_equal:date',
                'discount_percent' => 'nullable|numeric|min:0|max:100',
                'discount_amount' => 'nullable|numeric|min:0',
            ], [
                'client_id.required' => 'Veuillez sélectionner un client',
                'client_id.exists' => 'Le client sélectionné n\'existe pas',
                'products.required' => 'Veuillez ajouter au moins un produit',
                'products.min' => 'Veuillez ajouter au moins un produit',
            ]);

            // Gérer à la fois unit_prices et prices
            $pricesField = $request->has('unit_prices') ? 'unit_prices' : 'prices';

            // Générer une référence unique
            $reference = $request->input('reference') ?: Bill::generateReference();

            // Créer la facture
            $bill = Bill::create([
                'reference' => $reference,
                'client_id' => $validated['client_id'],
                'date' => $validated['date'],
                'due_date' => $request->input('due_date'),
                'tax_rate' => $validated['tax_rate'],
                'description' => $validated['notes'] ?? null,
                'status' => $request->input('status', 'pending'),
                'user_id' => $request->user() ? $request->user()->id : 1,
                'total' => 0, // Valeur par défaut pour éviter l'erreur MySQL
                'tax_amount' => 0, // Valeur par défaut pour éviter l'erreur MySQL
            ]);

            // Ajouter les produits
            $products = $request->input('products', []);
            $quantities = $request->input('quantities', []);
            $prices = $request->input($pricesField, []);

            // Vérification des données de produits
            if (count($products) != count($quantities) || count($products) != count($prices)) {
                throw new \Exception('Les données de produits sont incomplètes ou non valides.');
            }

            // Initialisation du total
            $subtotal = 0;

            for ($i = 0; $i < count($products); $i++) {
                if (empty($products[$i])) {
                    continue; // Ignorer les lignes vides
                }
                
                $productId = $products[$i];
                $quantity = floatval($quantities[$i]);
                $price = floatval($prices[$i]);
                
                if ($quantity <= 0 || $price < 0) {
                    continue; // Ignorer les quantités négatives ou nulles
                }
                
                $lineTotal = $quantity * $price;
                $subtotal += $lineTotal;
                
                $bill->products()->attach($productId, [
                    'quantity' => $quantity,
                    'unit_price' => $price,
                    'total' => $lineTotal
                ]);
                
                // Mise à jour du stock si nécessaire
                $product = Product::find($productId);
                if ($product && $product->type === 'physical' && $product->stock_quantity > 0) {
                    InventoryMovement::createExit(
                        $productId,
                        $quantity,
                        $price,
                        'Facture: ' . $bill->reference,
                        $bill->id,
                        'Vente via facture'
                    );
                }
            }

            // Appliquer la remise si présente
            $discount = 0;
            if ($request->filled('discount_percent')) {
                $discount = $subtotal * ($request->discount_percent / 100);
            } elseif ($request->filled('discount_amount')) {
                $discount = $request->discount_amount;
            }

            // Calculer les totaux
            $bill->calculateTotals();

            return redirect()
                ->route('bills.show', $bill)
                ->with('success', 'Facture créée avec succès');
                
        } catch (\Exception $e) {
            // Enregistrement de l'erreur pour le débogage
            Log::error('Erreur lors de la création de facture: ' . $e->getMessage(), [
                'data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la création de la facture: ' . $e->getMessage());
        }
    }

    public function show(Bill $bill)
    {
        $bill->load(['client', 'products', 'user']);
        return view('bills.show', compact('bill'));
    }

    public function edit(Bill $bill)
    {
        $clients = Client::orderBy('name')->get();
        $products = Product::orderBy('name')->get();
        $bill->load(['client', 'products']);

        return view('bills.edit', compact('bill', 'clients', 'products'));
    }

    public function update(Request $request, Bill $bill)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'date' => 'required|date',
            'tax_rate' => 'required|numeric',
            'description' => 'nullable|string',
            'products' => 'required|array',
            'quantities' => 'required|array',
            'prices' => 'required|array',
            'status' => 'nullable|string|in:pending,paid,cancelled',
        ]);

        // Sauvegarder les anciens produits pour les restaurer en stock si nécessaire
        $oldProducts = $bill->products->mapWithKeys(function ($product) {
            return [$product->id => $product->pivot->quantity];
        })->toArray();
        
        // Mettre à jour la facture
        $bill->update([
            'client_id' => $validated['client_id'],
            'date' => $validated['date'],
            'tax_rate' => $validated['tax_rate'],
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'] ?? $bill->status,
        ]);

        // Mettre à jour les produits
        $bill->products()->detach();
        
        $products = $request->input('products', []);
        $quantities = $request->input('quantities', []);
        $prices = $request->input('prices', []);

        for ($i = 0; $i < count($products); $i++) {
            if (isset($products[$i]) && isset($quantities[$i]) && isset($prices[$i])) {
                $productId = $products[$i];
                $quantity = $quantities[$i];
                $price = $prices[$i];
                
                $bill->products()->attach($productId, [
                    'quantity' => $quantity,
                    'unit_price' => $price,
                    'total' => $quantity * $price
                ]);
                
                // Gestion du stock pour les produits modifiés
                $product = Product::find($productId);
                if ($product && $product->type === 'physical') {
                    $oldQuantity = $oldProducts[$productId] ?? 0;
                    
                    // Si le produit était déjà dans la facture, on ajuste la différence
                    if (array_key_exists($productId, $oldProducts)) {
                        $quantityDiff = $quantity - $oldQuantity;
                        
                        if ($quantityDiff > 0) {
                            // Retirer la différence supplémentaire du stock
                            InventoryMovement::createExit(
                                $productId,
                                $quantityDiff,
                                $price,
                                'Facture modifiée: ' . $bill->reference,
                                $bill->id,
                                'Ajustement après modification de facture'
                            );
                        } elseif ($quantityDiff < 0) {
                            // Remettre en stock la différence
                            InventoryMovement::createEntry(
                                $productId,
                                abs($quantityDiff),
                                $price,
                                'Facture modifiée: ' . $bill->reference,
                                'Ajustement après modification de facture'
                            );
                        }
                        
                        // Supprimer de la liste des anciens produits
                        unset($oldProducts[$productId]);
                    } else {
                        // Nouveau produit ajouté à la facture
                        InventoryMovement::createExit(
                            $productId,
                            $quantity,
                            $price,
                            'Facture modifiée: ' . $bill->reference,
                            $bill->id,
                            'Nouveau produit ajouté à la facture'
                        );
                    }
                }
            }
        }
        
        // Remettre en stock les produits qui ont été supprimés de la facture
        foreach ($oldProducts as $productId => $quantity) {
            $product = Product::find($productId);
            if ($product && $product->type === 'physical') {
                InventoryMovement::createEntry(
                    $productId,
                    $quantity,
                    null,
                    'Facture modifiée: ' . $bill->reference,
                    'Produit retiré de la facture'
                );
            }
        }
        
        // Recalculer les totaux
        $bill->calculateTotals();

        return redirect()
            ->route('bills.show', $bill)
            ->with('success', 'Facture mise à jour avec succès');
    }

    public function destroy(Bill $bill)
    {
        $bill->delete();
        return redirect()
            ->route('bills.index')
            ->with('success', 'Facture supprimée avec succès');
    }

    /**
     * Mettre à jour le statut d'une facture
     */
    public function updateStatus(Request $request, Bill $bill)
    {
        $request->validate([
            'status' => 'required|in:pending,paid,cancelled'
        ]);

        $oldStatus = $bill->status;
        $newStatus = $request->status;
        
        // Mettre à jour le statut
        $bill->update(['status' => $newStatus]);
        
        // Messages personnalisés selon le changement de statut
        if ($oldStatus !== $newStatus) {
            if ($newStatus === 'paid') {
                $message = 'La facture a été marquée comme payée.';
            } elseif ($newStatus === 'pending') {
                $message = 'La facture a été marquée comme en attente de paiement.';
            } elseif ($newStatus === 'cancelled') {
                $message = 'La facture a été annulée.';
            }
        } else {
            $message = 'Le statut de la facture a été mis à jour.';
        }
        
        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => $message]);
        }
        
        return redirect()
            ->route('bills.show', $bill)
            ->with('success', $message);
    }

    public function downloadPdf(Bill $bill)
    {
        $bill->load(['client', 'products', 'user']);
        $settings = Setting::first();
        
        // Conversion du chemin Storage en chemin réel pour l'accès du PDF
        if ($settings && $settings->logo_path) {
            $logoRealPath = storage_path('app/public/' . $settings->logo_path);
            $settings->logo_real_path = $logoRealPath;
        }
        
        $pdf = PDF::loadView('bills.pdf', compact('bill', 'settings'));
        return $pdf->download("facture-{$bill->reference}.pdf");
    }

    /**
     * Exporter les factures au format CSV
     * 
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function export()
    {
        $bills = Bill::with(['client', 'products'])->get();
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=factures-export.csv',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];
        
        $callback = function() use ($bills) {
            $file = fopen('php://output', 'w');
            
            // En-têtes CSV
            fputcsv($file, [
                'ID',
                'Référence',
                'Client',
                'Date',
                'Date d\'échéance',
                'Statut',
                'Sous-total',
                'Taxes',
                'Total',
                'Produits',
                'Notes',
                'Date de création'
            ]);
            
            // Données
            foreach ($bills as $bill) {
                $products = $bill->products->map(function($product) {
                    return $product->name . ' (' . $product->pivot->quantity . ' x ' . number_format($product->pivot->price, 2) . ')';
                })->implode('; ');
                
                fputcsv($file, [
                    $bill->id,
                    $bill->reference,
                    $bill->client ? $bill->client->name : 'Client inconnu',
                    $bill->date ? $bill->date->format('Y-m-d') : '',
                    $bill->due_date ? $bill->due_date->format('Y-m-d') : '',
                    $bill->status,
                    $bill->subtotal,
                    $bill->tax_amount,
                    $bill->total,
                    $products,
                    $bill->notes,
                    $bill->created_at->format('Y-m-d H:i:s')
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}

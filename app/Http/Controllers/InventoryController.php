<?php

namespace App\Http\Controllers;

use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    /**
     * Afficher le tableau de bord de l'inventaire
     */
    public function index()
    {
        // Statistiques globales de stock (uniquement produits physiques)
        $stats = [
            'total_products' => Product::where('type', 'physical')->count(),
            'out_of_stock' => Product::where('type', 'physical')->where('stock_quantity', '<=', 0)->count(),
            'low_stock' => Product::where('type', 'physical')
                ->whereColumn('stock_quantity', '<=', 'stock_alert_threshold')
                ->where('stock_alert_threshold', '>', 0)
                ->count(),
            'total_stock_value' => Product::where('type', 'physical')
                ->selectRaw('SUM(stock_quantity * default_price) as value')
                ->first()->value ?? 0,
            'total_cost_value' => Product::where('type', 'physical')
                ->selectRaw('SUM(stock_quantity * cost_price) as value')
                ->first()->value ?? 0,
        ];

        // Produits physiques en rupture ou faible stock 
        $lowStockProducts = Product::where('type', 'physical')
            ->where(function($query) {
                $query->whereColumn('stock_quantity', '<=', 'stock_alert_threshold')
                    ->where('stock_alert_threshold', '>', 0)
                    ->orWhere('stock_quantity', '<=', 0);
            })
            ->with('category')
            ->orderBy('stock_quantity')
            ->get();

        // Derniers mouvements de stock
        $recentMovements = InventoryMovement::with(['product', 'user'])
            ->latest()
            ->paginate(10);

        // Catégories pour les filtres
        $categories = ProductCategory::orderBy('name')->get();

        return view('inventory.index', compact('stats', 'lowStockProducts', 'recentMovements', 'categories'));
    }

    /**
     * Afficher la liste des mouvements de stock
     */
    public function movements(Request $request)
    {
        $query = InventoryMovement::with(['product', 'user', 'bill']);

        // Filtres
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Tri
        $query->latest();

        // Nombre d'éléments par page (défaut: 25)
        $perPage = $request->input('per_page', 25);
        // Limiter les valeurs possibles pour éviter les problèmes de performance
        $perPage = in_array($perPage, [25, 50, 100, 200]) ? $perPage : 25;

        $movements = $query->paginate($perPage)->appends($request->except('page'));
        // Ne lister que les produits physiques dans les filtres
        $products = Product::where('type', 'physical')->orderBy('name')->get();

        return view('inventory.movements', compact('movements', 'products', 'perPage'));
    }

    /**
     * Afficher le formulaire d'ajustement de stock
     */
    public function adjustment()
    {
        // Ne lister que les produits physiques pour l'ajustement de stock
        $products = Product::where('type', 'physical')->orderBy('name')->get();
        $categories = ProductCategory::orderBy('name')->get();

        return view('inventory.adjustment', compact('products', 'categories'));
    }

    /**
     * Traiter un ajustement de stock
     */
    public function processAdjustment(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'adjustment_type' => 'required|in:add,subtract,set',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $notes = $validated['notes'] ?? 'Ajustement manuel';

        // Traiter selon le type d'ajustement
        switch ($validated['adjustment_type']) {
            case 'add':
                InventoryMovement::createEntry(
                    $product->id,
                    $validated['quantity'],
                    null,
                    'Ajustement manuel: ajout',
                    $notes
                );
                $message = 'Ajout de ' . $validated['quantity'] . ' unités au stock';
                break;
            case 'subtract':
                if ($product->stock_quantity < $validated['quantity']) {
                    return back()->with('error', 'Quantité insuffisante en stock');
                }
                InventoryMovement::createExit(
                    $product->id,
                    $validated['quantity'],
                    null,
                    'Ajustement manuel: retrait',
                    null,
                    $notes
                );
                $message = 'Retrait de ' . $validated['quantity'] . ' unités du stock';
                break;
            case 'set':
                InventoryMovement::createAdjustment(
                    $product->id,
                    $validated['quantity'],
                    $notes
                );
                $message = 'Stock ajusté à ' . $validated['quantity'] . ' unités';
                break;
        }

        return redirect()
            ->route('inventory.index')
            ->with('success', $message);
    }

    /**
     * Recevoir un stock (entrée en stock)
     */
    public function receive()
    {
        // Ne lister que les produits physiques pour la réception de stock
        $products = Product::where('type', 'physical')->orderBy('name')->get();
        $categories = ProductCategory::orderBy('name')->get();

        return view('inventory.receive', compact('products', 'categories'));
    }

    /**
     * Traiter une réception de stock
     */
    public function processReceive(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|array',
            'product_id.*' => 'required|exists:products,id',
            'quantity' => 'required|array',
            'quantity.*' => 'required|integer|min:1',
            'cost_price' => 'nullable|array',
            'cost_price.*' => 'nullable|numeric|min:0',
            'reference' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $reference = $validated['reference'] ?? 'Réception de stock ' . now()->format('Y-m-d H:i:s');
        $notes = $validated['notes'] ?? 'Réception de stock';

        for ($i = 0; $i < count($validated['product_id']); $i++) {
            if (isset($validated['product_id'][$i]) && isset($validated['quantity'][$i])) {
                $productId = $validated['product_id'][$i];
                $quantity = $validated['quantity'][$i];
                $costPrice = $validated['cost_price'][$i] ?? null;

                InventoryMovement::createEntry(
                    $productId,
                    $quantity,
                    $costPrice,
                    $reference,
                    $notes
                );

                // Mettre à jour le prix de revient si nécessaire
                if ($costPrice) {
                    $product = Product::find($productId);
                    $product->cost_price = $costPrice;
                    $product->save();
                }
            }
        }

        return redirect()
            ->route('inventory.index')
            ->with('success', 'Réception de stock enregistrée avec succès');
    }

    /**
     * Obtenir les informations sur le stock d'un produit (API)
     */
    public function getProductStock($productId)
    {
        $product = Product::findOrFail($productId);
        $movements = InventoryMovement::where('product_id', $productId)
            ->latest()
            ->limit(10)
            ->get();

        return response()->json([
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'stock_quantity' => $product->stock_quantity,
                'stock_alert_threshold' => $product->stock_alert_threshold,
                'low_stock' => $product->isLowStock(),
                'out_of_stock' => $product->isOutOfStock(),
            ],
            'movements' => $movements,
        ]);
    }
}

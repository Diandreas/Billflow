<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::withCount('bills')
            ->withSum('bills as total_sales', DB::raw('bill_products.unit_price * bill_products.quantity'))
            ->select('products.*'); // S'assurer que toutes les colonnes sont chargées pour les méthodes isLowStock et isOutOfStock

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
        if ($request->filled('sort')) {
            $sortField = $request->sort;
            $sortDirection = $request->direction ?? 'asc';

            if ($sortField === 'total_sales') {
                $query->orderBy('total_sales', $sortDirection);
            } elseif ($sortField === 'usage_count') {
                $query->orderBy('bills_count', $sortDirection);
            } else {
                $query->orderBy($sortField, $sortDirection);
            }
        } else {
            $query->latest();
        }

        $products = $query->paginate(10)->withQueryString();

        // Statistiques globales
        $stats = [
            'total_products' => Product::count(),
            'active_products' => Product::has('bills')->count(),
            'physical_products' => Product::where('type', 'physical')->count(),
            'total_revenue' => DB::table('bill_products')
                ->join('products', 'bill_products.product_id', '=', 'products.id')
                ->sum(DB::raw('unit_price * quantity')),
            'average_price' => DB::table('bill_products')
                ->join('products', 'bill_products.product_id', '=', 'products.id')
                ->avg('unit_price')
        ];

        // Top produits par ventes
        $topProducts = Product::withCount('bills')
            ->withSum('bills as total_sales', DB::raw('bill_products.unit_price * bill_products.quantity'))
            ->orderByDesc('total_sales')
            ->limit(5)
            ->get();

        return view('products.index', compact('products', 'stats', 'topProducts'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
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
        ]);

        // S'assurer que le prix par défaut n'est jamais NULL
        if (!isset($validated['default_price']) || $validated['default_price'] === null) {
            $validated['default_price'] = 0;
        }

        // Si le type n'est pas spécifié, considérer comme un service par défaut
        if (empty($validated['type'])) {
            $validated['type'] = 'service';
        }

        // Pour les services, mettre les valeurs de stock à zéro
        if ($validated['type'] === 'service') {
            $validated['stock_quantity'] = 0;
            $validated['stock_alert_threshold'] = 0;
        }

        $product = Product::create($validated);

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
        // Charger toutes les factures sans pagination
        $invoices = $product->bills()->with('client')->latest('date')->get();
        
        // Statistiques du produit
        $stats = [
            'total_sales' => $invoices->sum(function($bill) {
                return $bill->pivot->unit_price * $bill->pivot->quantity;
            }),
            'total_quantity' => $invoices->sum('pivot.quantity'),
            'average_price' => $invoices->avg('pivot.unit_price'),
            'usage_count' => $invoices->count(),
            'first_use' => $invoices->last()?->date,
            'last_use' => $invoices->first()?->date,
        ];

        // Évolution mensuelle
        $monthlyStats = $invoices
            ->groupBy(function($bill) {
                return $bill->date->format('Y-m');
            })
            ->map(function($bills) {
                return [
                    'count' => $bills->count(),
                    'total' => $bills->sum(function($bill) {
                        return $bill->pivot->unit_price * $bill->pivot->quantity;
                    }),
                    'quantity' => $bills->sum('pivot.quantity'),
                    'average_price' => $bills->avg('pivot.unit_price')
                ];
            });

        // Prix utilisés
        $priceHistory = DB::table('bill_products')
            ->select('unit_price', DB::raw('COUNT(*) as usage_count'))
            ->where('product_id', $product->id)
            ->groupBy('unit_price')
            ->orderByDesc('usage_count')
            ->get();

        return view('products.show', compact('product', 'stats', 'monthlyStats', 'priceHistory', 'invoices'));
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
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
        ]);

        // Si le type n'est pas spécifié, considérer comme un service par défaut
        if (empty($validated['type'])) {
            $validated['type'] = 'service';
        }

        // Pour les services, mettre les valeurs de stock à zéro
        if ($validated['type'] === 'service') {
            $validated['stock_quantity'] = 0;
            $validated['stock_alert_threshold'] = 0;
        }

        $product->update($validated);

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

        return response()->json([
            'success' => true,
            'product' => $product,
            'message' => 'Produit créé avec succès'
        ]);
    }
}

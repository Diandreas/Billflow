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
            ->withSum('bills as total_sales', DB::raw('bill_products.unit_price * bill_products.quantity'));

        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
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
        ]);

        $product = Product::create($validated);

        if ($request->ajax()) {
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
        $product->load(['bills' => function($query) {
            $query->latest()->with('client');
        }]);

        // Statistiques du produit
        $stats = [
            'total_sales' => $product->bills->sum(function($bill) {
                return $bill->pivot->unit_price * $bill->pivot->quantity;
            }),
            'total_quantity' => $product->bills->sum('pivot.quantity'),
            'average_price' => $product->bills->avg('pivot.unit_price'),
            'usage_count' => $product->bills->count(),
            'first_use' => $product->bills->last()?->date,
            'last_use' => $product->bills->first()?->date,
        ];

        // Évolution mensuelle
        $monthlyStats = $product->bills
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

        return view('products.show', compact('product', 'stats', 'monthlyStats', 'priceHistory'));
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
        ]);

        $product->update($validated);

        return redirect()
            ->route('products.show', $product)
            ->with('success', 'Produit mis à jour avec succès');
    }

    public function destroy(Product $product)
    {
        // Vérifier si le produit est utilisé dans des factures
        if ($product->bills()->exists()) {
            return back()->with('error', 'Impossible de supprimer un produit utilisé dans des factures');
        }

        $product->delete();

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

        $product = Product::create($validated);

        return response()->json([
            'success' => true,
            'product' => $product,
            'message' => 'Produit créé avec succès'
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

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

        // Préparer les statistiques pour la vue
        $stats = [
            'total_products' => Product::count(),
            'active_products' => Product::where('status', 'actif')->count(),
            'physical_products' => Product::where('type', 'physical')->count(),
            'total_revenue' => Product::withSum('bills as total_sales', DB::raw('bill_items.quantity * bill_items.price'))->sum('total_sales'),
            'average_price' => Product::avg('default_price') ?: 0,
        ];

        return view('products.index', compact('products', 'shops', 'stats'));
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
                return $bill->pivot->quantity * $bill->pivot->price;
            }),
            'total_quantity' => $invoices->sum('pivot.quantity'),
            'average_price' => $invoices->avg('pivot.price'),
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
                        return $bill->pivot->quantity * $bill->pivot->price;
                    }),
                    'quantity' => $bills->sum('pivot.quantity'),
                    'average_price' => $bills->avg('pivot.price')
                ];
            });

        // Prix utilisés
        $priceHistory = DB::table('bill_items')
            ->join('bills', 'bill_items.bill_id', '=', 'bills.id')
            ->where('bill_items.product_id', $product->id)
            ->orderBy('bills.date', 'desc')
            ->select('bills.date', 'bill_items.price')
            ->limit(10)
            ->get();

        // Obtenir les trocs associés
        $barterItems = $product->barterItems()->with('barter')->latest()->take(5)->get();

        return view('products.show', compact('product', 'stats', 'monthlyStats', 'priceHistory', 'invoices', 'barterItems'));
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
            'is_barterable' => 'nullable|boolean',
        ]);

        // S'assurer que le prix par défaut n'est jamais NULL
        if (!isset($validated['default_price']) || $validated['default_price'] === null) {
            $validated['default_price'] = 0;
        }

        // Si le type change pour 'service', réinitialiser les valeurs de stock et troc
        if (isset($validated['type']) && $validated['type'] === 'service') {
            $validated['stock_quantity'] = 0;
            $validated['stock_alert_threshold'] = 0;
            $validated['is_barterable'] = false;
        }

        // Conversion de la valeur de is_barterable
        $validated['is_barterable'] = isset($validated['is_barterable']) && $validated['is_barterable'] ? true : false;

        $product->update($validated);

        if ($request->ajax() || $request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'product' => $product,
                'message' => 'Produit mis à jour avec succès'
            ]);
        }

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

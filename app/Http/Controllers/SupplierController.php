<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Vérifier les permissions
        if (Gate::denies('manage-suppliers') && Gate::denies('admin')) {
            abort(403, 'Action non autorisée.');
        }

        $query = Supplier::query();

        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('contact_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filtre par statut
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Tri
        $sortField = $request->input('sort', 'name');
        $sortDirection = $request->input('direction', 'asc');
        $query->orderBy($sortField, $sortDirection);

        $suppliers = $query->withCount('products')->paginate(15)->withQueryString();

        // Statistiques globales
        $stats = [
            'total_suppliers' => Supplier::count(),
            'active_suppliers' => Supplier::where('status', 'actif')->count(),
            'total_products' => Product::whereNotNull('supplier_id')->count(),
            'suppliers_with_products' => Supplier::has('products')->count(),
        ];

        return view('suppliers.index', compact('suppliers', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Vérifier les permissions
        if (Gate::denies('manage-suppliers') && Gate::denies('admin')) {
            abort(403, 'Action non autorisée.');
        }

        return view('suppliers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Vérifier les permissions
        if (Gate::denies('manage-suppliers') && Gate::denies('admin')) {
            abort(403, 'Action non autorisée.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'tax_id' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
            'status' => 'nullable|string|in:actif,inactif',
            'website' => 'nullable|url|max:255',
        ]);

        // Status par défaut
        if (!isset($validated['status'])) {
            $validated['status'] = 'actif';
        }

        $supplier = Supplier::create($validated);

        if ($request->ajax() || $request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'supplier' => $supplier,
                'message' => 'Fournisseur créé avec succès'
            ]);
        }

        return redirect()
            ->route('suppliers.show', $supplier)
            ->with('success', 'Fournisseur créé avec succès');
    }

    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier)
    {
        // Vérifier les permissions
        if (Gate::denies('view-suppliers') && Gate::denies('admin')) {
            abort(403, 'Action non autorisée.');
        }

        // Chargement des produits associés
        $products = $supplier->products()->paginate(10);

        // Statistiques du fournisseur
        $stats = [
            'product_count' => $supplier->products()->count(),
            'physical_products' => $supplier->products()->where('type', 'physical')->count(),
            'service_products' => $supplier->products()->where('type', 'service')->count(),
            'stock_value' => $supplier->products()
                ->where('type', 'physical')
                ->sum(DB::raw('stock_quantity * cost_price')),
            'average_price' => $supplier->products()->avg('default_price') ?: 0,
        ];

        return view('suppliers.show', compact('supplier', 'products', 'stats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier)
    {
        // Vérifier les permissions
        if (Gate::denies('manage-suppliers') && Gate::denies('admin')) {
            abort(403, 'Action non autorisée.');
        }

        return view('suppliers.edit', compact('supplier'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Supplier $supplier)
    {
        // Vérifier les permissions
        if (Gate::denies('manage-suppliers') && Gate::denies('admin')) {
            abort(403, 'Action non autorisée.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'tax_id' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
            'status' => 'nullable|string|in:actif,inactif',
            'website' => 'nullable|url|max:255',
        ]);

        $supplier->update($validated);

        if ($request->ajax() || $request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'supplier' => $supplier,
                'message' => 'Fournisseur mis à jour avec succès'
            ]);
        }

        return redirect()
            ->route('suppliers.show', $supplier)
            ->with('success', 'Fournisseur mis à jour avec succès');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        // Vérifier les permissions
        if (Gate::denies('manage-suppliers') && Gate::denies('admin')) {
            abort(403, 'Action non autorisée.');
        }

        // Vérifier si le fournisseur est utilisé par des produits
        if ($supplier->products()->exists()) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de supprimer un fournisseur associé à des produits'
                ], 422);
            }
            return back()->with('error', 'Impossible de supprimer un fournisseur associé à des produits');
        }

        $supplier->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Fournisseur supprimé avec succès'
            ]);
        }

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'Fournisseur supprimé avec succès');
    }

    /**
     * Recherche des fournisseurs (API pour AJAX)
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        $suppliers = Supplier::where('name', 'like', "%{$query}%")
            ->orWhere('contact_name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->limit(10)
            ->get()
            ->map(function($supplier) {
                return [
                    'id' => $supplier->id,
                    'name' => $supplier->name,
                    'contact' => $supplier->contact_name,
                    'email' => $supplier->email,
                    'product_count' => $supplier->products()->count()
                ];
            });

        return response()->json($suppliers);
    }

    /**
     * Obtenir les statistiques des fournisseurs pour le tableau de bord admin
     */
    public function getStats()
    {
        // Vérifier les permissions
        if (Gate::denies('view-suppliers') && Gate::denies('admin')) {
            abort(403, 'Action non autorisée.');
        }

        // Top 5 des fournisseurs par nombre de produits
        $topByProductCount = Supplier::withCount('products')
            ->orderByDesc('products_count')
            ->limit(5)
            ->get();

        // Top 5 des fournisseurs par valeur de stock
        $topByStockValue = Supplier::withCount('products')
            ->join('products', 'suppliers.id', '=', 'products.supplier_id')
            ->where('products.type', 'physical')
            ->select('suppliers.*', DB::raw('SUM(products.stock_quantity * products.cost_price) as stock_value'))
            ->groupBy('suppliers.id')
            ->orderByDesc('stock_value')
            ->limit(5)
            ->get();

        // Les 5 derniers fournisseurs ajoutés
        $latest = Supplier::latest()->limit(5)->get();

        return response()->json([
            'top_by_product_count' => $topByProductCount,
            'top_by_stock_value' => $topByStockValue,
            'latest' => $latest
        ]);
    }

    /**
     * Création rapide d'un fournisseur via AJAX
     */
    public function quickCreate(Request $request)
    {
        // Vérifier les permissions
        if (Gate::denies('manage-suppliers') && Gate::denies('admin')) {
            abort(403, 'Action non autorisée.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
        ]);

        // Status par défaut à actif
        $validated['status'] = 'actif';

        $supplier = Supplier::create($validated);

        return response()->json([
            'success' => true,
            'supplier' => $supplier,
            'message' => 'Fournisseur créé avec succès'
        ]);
    }
} 
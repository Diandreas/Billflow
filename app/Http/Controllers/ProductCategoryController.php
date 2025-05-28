<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductCategoryController extends Controller
{
    /**
     * Afficher toutes les catégories de produits
     */
    public function index()
    {
        $categories = ProductCategory::withCount('products')
            ->orderBy('name')
            ->get();
        
        return view('product-categories.index', compact('categories'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $categories = ProductCategory::orderBy('name')->get();
        return view('product-categories.create', compact('categories'));
    }

    /**
     * Enregistrer une nouvelle catégorie
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'accounting_code' => 'nullable|string|max:50',
            'tax_code' => 'nullable|string|max:50',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:50',
            'parent_id' => 'nullable|exists:product_categories,id',
        ]);

        // Générer le slug à partir du nom
        $validated['slug'] = Str::slug($validated['name']);

        // Créer la catégorie
        $category = ProductCategory::create($validated);

        return redirect()
            ->route('product-categories.index')
            ->with('success', 'Catégorie créée avec succès');
    }

    /**
     * Afficher une catégorie
     */
    public function show(ProductCategory $productCategory)
    {
        $productCategory->load(['products', 'parent', 'children']);
        return view('product-categories.show', compact('productCategory'));
    }

    /**
     * Afficher le formulaire de modification
     */
    public function edit(ProductCategory $productCategory)
    {
        $categories = ProductCategory::where('id', '!=', $productCategory->id)
            ->orderBy('name')
            ->get();
            
        return view('product-categories.edit', compact('productCategory', 'categories'));
    }

    /**
     * Mettre à jour une catégorie
     */
    public function update(Request $request, ProductCategory $productCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'accounting_code' => 'nullable|string|max:50',
            'tax_code' => 'nullable|string|max:50',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:50',
            'parent_id' => 'nullable|exists:product_categories,id',
        ]);

        // Mettre à jour le slug si le nom a changé
        if ($request->name != $productCategory->name) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Éviter une boucle infinie
        if ($validated['parent_id'] == $productCategory->id) {
            $validated['parent_id'] = null;
        }

        $productCategory->update($validated);

        return redirect()
            ->route('product-categories.index')
            ->with('success', 'Catégorie mise à jour avec succès');
    }

    /**
     * Supprimer une catégorie
     */
    public function destroy(ProductCategory $productCategory)
    {
        // Vérifier si la catégorie a des produits
        if ($productCategory->products()->exists()) {
            return back()->with('error', 'Impossible de supprimer une catégorie avec des produits associés');
        }

        // Mettre à jour les catégories enfants
        $productCategory->children()->update(['parent_id' => $productCategory->parent_id]);

        $productCategory->delete();

        return redirect()
            ->route('product-categories.index')
            ->with('success', 'Catégorie supprimée avec succès');
    }

    /**
     * API pour récupérer toutes les catégories
     */
    public function getAll()
    {
        $categories = ProductCategory::withCount('products')
            ->orderBy('name')
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'description' => $category->description,
                    'parent_id' => $category->parent_id,
                    'products_count' => $category->products_count,
                    'icon' => $category->icon,
                    'color' => $category->color,
                ];
            });

        return response()->json($categories);
    }

    /**
     * Création rapide d'une catégorie via AJAX
     */
    public function quickCreate(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Générer le slug à partir du nom
        $validated['slug'] = Str::slug($validated['name']);

        // Créer la catégorie
        $category = ProductCategory::create($validated);

        return response()->json([
            'success' => true,
            'category' => $category,
            'message' => 'Catégorie créée avec succès'
        ]);
    }
}

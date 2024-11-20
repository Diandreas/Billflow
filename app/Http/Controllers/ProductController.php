<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $product = Product::create($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'product' => $product,
                'message' => 'Produit créé avec succès'
            ]);
        }

        return redirect()->back()->with('success', 'Produit créé avec succès');
    }

    // API endpoint pour la recherche de produits
    public function search(Request $request)
    {
        $query = $request->get('q');
        $products = Product::where('name', 'like', "%{$query}%")
            ->take(5)
            ->get();
        return response()->json($products);
    }
}

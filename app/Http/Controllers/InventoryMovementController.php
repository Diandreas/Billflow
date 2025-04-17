<?php

namespace App\Http\Controllers;

use App\Models\InventoryMovement;
use App\Models\Shop;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

class InventoryMovementController extends Controller
{
    public function index(Request $request)
    {
        $query = InventoryMovement::with(['product', 'user', 'shop']);

        // Filtrer par boutique pour les non-administrateurs
        if (!Gate::allows('admin')) {
            $shopIds = Auth::user()->shops->pluck('id')->toArray();
            $query->whereIn('shop_id', $shopIds);
        }

        // Filtres
        if ($request->filled('shop_id')) {
            $query->where('shop_id', $request->shop_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Tri par défaut: date de création descendante
        $query->latest();

        $movements = $query->paginate(15);
        
        // Pour les filtres
        $shops = Gate::allows('admin') 
            ? Shop::all() 
            : Auth::user()->shops;
        
        $products = Product::orderBy('name')->get();
        $movementTypes = [
            'achat' => 'Achat',
            'vente' => 'Vente',
            'ajustement' => 'Ajustement',
            'transfert' => 'Transfert'
        ];

        return view('inventory.index', compact(
            'movements', 
            'shops', 
            'products', 
            'movementTypes'
        ));
    }
} 
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class InventoryMovement extends Model
{
    protected $fillable = [
        'product_id',
        'type',
        'quantity',
        'reference',
        'bill_id',
        'user_id',
        'notes',
        'unit_price',
        'total_price',
        'stock_before',
        'stock_after',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'stock_before' => 'integer',
        'stock_after' => 'integer',
    ];

    /**
     * Obtenir le produit associé
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Obtenir la facture associée
     */
    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    /**
     * Obtenir l'utilisateur associé
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Créer un mouvement d'entrée en stock
     */
    public static function createEntry($productId, $quantity, $unitPrice = null, $reference = null, $notes = null, $userId = null)
    {
        $product = Product::findOrFail($productId);
        $stockBefore = $product->stock_quantity;
        $stockAfter = $stockBefore + $quantity;
        
        $totalPrice = $unitPrice ? $unitPrice * $quantity : null;
        
        $movement = self::create([
            'product_id' => $productId,
            'type' => 'entrée',
            'quantity' => $quantity,
            'reference' => $reference,
            'user_id' => $userId ?? (Auth::check() ? Auth::id() : null),
            'notes' => $notes,
            'unit_price' => $unitPrice,
            'total_price' => $totalPrice,
            'stock_before' => $stockBefore,
            'stock_after' => $stockAfter,
        ]);
        
        // Mise à jour du stock
        $product->update(['stock_quantity' => $stockAfter]);
        
        return $movement;
    }
    
    /**
     * Créer un mouvement de sortie de stock
     */
    public static function createExit($productId, $quantity, $unitPrice = null, $reference = null, $billId = null, $notes = null, $userId = null)
    {
        $product = Product::findOrFail($productId);
        $stockBefore = $product->stock_quantity;
        $stockAfter = $stockBefore - $quantity;
        
        $totalPrice = $unitPrice ? $unitPrice * $quantity : null;
        
        $movement = self::create([
            'product_id' => $productId,
            'type' => 'sortie',
            'quantity' => $quantity,
            'reference' => $reference,
            'bill_id' => $billId,
            'user_id' => $userId ?? (Auth::check() ? Auth::id() : null),
            'notes' => $notes,
            'unit_price' => $unitPrice,
            'total_price' => $totalPrice,
            'stock_before' => $stockBefore,
            'stock_after' => $stockAfter,
        ]);
        
        // Mise à jour du stock
        $product->update(['stock_quantity' => $stockAfter]);
        
        return $movement;
    }
    
    /**
     * Créer un mouvement d'ajustement de stock
     */
    public static function createAdjustment($productId, $newQuantity, $notes = null, $userId = null)
    {
        $product = Product::findOrFail($productId);
        $stockBefore = $product->stock_quantity;
        $stockAfter = $newQuantity;
        $quantity = $stockAfter - $stockBefore;
        
        $movement = self::create([
            'product_id' => $productId,
            'type' => 'ajustement',
            'quantity' => $quantity,
            'reference' => 'Ajustement manuel',
            'user_id' => $userId ?? (Auth::check() ? Auth::id() : null),
            'notes' => $notes,
            'stock_before' => $stockBefore,
            'stock_after' => $stockAfter,
        ]);
        
        // Mise à jour du stock
        $product->update(['stock_quantity' => $stockAfter]);
        
        return $movement;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarterReceivedItem extends Model
{
    use HasFactory;

    protected $table = 'barter_received_items';

    protected $fillable = [
        'barter_id',
        'product_id',
        'quantity',
        'unit_price',
        'total_price',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'quantity' => 'integer',
    ];

    /**
     * Relation avec le troc
     */
    public function barter()
    {
        return $this->belongsTo(Barter::class);
    }

    /**
     * Relation avec le produit
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Calcule le prix total (quantité * prix unitaire)
     */
    public function calculateTotalPrice()
    {
        $this->total_price = $this->quantity * $this->unit_price;
        $this->save();
        return $this->total_price;
    }
} 
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarterItem extends Model
{
    protected $fillable = [
        'barter_id',
        'product_id',
        'name',
        'description',
        'type',
        'value',
        'quantity'
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'quantity' => 'integer'
    ];

    /**
     * Relation avec le troc auquel cet article est lié
     */
    public function barter()
    {
        return $this->belongsTo(Barter::class);
    }

    /**
     * Relation avec le produit (si applicable)
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Calcule la valeur totale de l'article
     */
    public function getTotalValueAttribute()
    {
        return $this->value * $this->quantity;
    }

    /**
     * Détermine si cet article est donné par le client
     */
    public function isGiven()
    {
        return $this->type === 'given';
    }

    /**
     * Détermine si cet article est reçu par le client
     */
    public function isReceived()
    {
        return $this->type === 'received';
    }
} 
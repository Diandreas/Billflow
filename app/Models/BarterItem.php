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
     * Relation avec les images de cet article
     */
    public function images()
    {
        return $this->hasMany(BarterItemImage::class);
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

    /**
     * Retourne l'URL de la première image ou une image par défaut
     */
    public function getMainImageUrlAttribute()
    {
        $firstImage = $this->images()->orderBy('order')->first();
        
        if ($firstImage) {
            return $firstImage->url;
        }
        
        // Si c'est un produit, essayer d'utiliser son image
        if ($this->product_id) {
            // Cette partie dépend de comment vous gérez les images de produits
            // Adaptez selon votre logique d'images de produits
            return asset('images/products/default.png');
        }
        
        return asset('images/no-image.png');
    }
} 
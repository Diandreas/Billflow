<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarterGivenItem extends Model
{
    use HasFactory;

    protected $table = 'barter_given_items';

    protected $fillable = [
        'barter_id',
        'name',
        'description',
        'quantity',
        'estimated_value',
        'images',
        'condition',
        'type',
        'product_id',
    ];

    protected $casts = [
        'estimated_value' => 'decimal:2',
        'quantity' => 'integer',
        'images' => 'array',
    ];

    /**
     * Relation avec le troc
     */
    public function barter()
    {
        return $this->belongsTo(Barter::class);
    }

    /**
     * Relation avec le produit associé (si applicable)
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Calcule la valeur totale de cet article (quantité * valeur estimée)
     */
    public function getTotalValueAttribute()
    {
        return $this->quantity * $this->estimated_value;
    }

    /**
     * Accesseur pour obtenir les URL complètes des images
     */
    public function getImagesUrlAttribute()
    {
        if (!$this->images) {
            return [];
        }

        return array_map(function ($image) {
            return asset('storage/' . $image);
        }, $this->images);
    }

    /**
     * Accesseur pour obtenir la première image ou une image par défaut
     */
    public function getMainImageUrlAttribute()
    {
        if (!$this->images || empty($this->images)) {
            return asset('images/no-image.png');
        }

        return asset('storage/' . $this->images[0]);
    }

    /**
     * Obtenir le nom formaté de l'état
     */
    public function getFormattedConditionAttribute()
    {
        $conditions = [
            'neuf' => 'Neuf',
            'comme_neuf' => 'Comme neuf',
            'bon' => 'Bon état',
            'acceptable' => 'État acceptable',
            'mauvais' => 'Mauvais état',
        ];

        return $conditions[$this->condition] ?? $this->condition;
    }
} 
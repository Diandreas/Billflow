<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductModel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'brand_id',
        'technical_specs',
        'year'
    ];

    /**
     * Relation avec la marque de ce modèle
     */
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Relation avec les produits de ce modèle
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Retourne le nom complet du modèle (avec la marque)
     */
    public function getFullNameAttribute()
    {
        return $this->brand->name . ' ' . $this->name;
    }
}

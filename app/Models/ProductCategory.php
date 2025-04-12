<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'accounting_code',
        'tax_code',
        'icon',
        'color',
        'parent_id',
    ];

    /**
     * Obtenir les sous-catégories
     */
    public function children()
    {
        return $this->hasMany(ProductCategory::class, 'parent_id');
    }

    /**
     * Obtenir la catégorie parente
     */
    public function parent()
    {
        return $this->belongsTo(ProductCategory::class, 'parent_id');
    }

    /**
     * Obtenir tous les produits de cette catégorie
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'logo_url',
        'website',
        'origin_country'
    ];

    /**
     * Relation avec les modèles de cette marque
     */
    public function models()
    {
        return $this->hasMany(ProductModel::class);
    }

    /**
     * Relation avec les produits directement liés à cette marque
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}

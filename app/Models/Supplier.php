<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'contact_name',
        'email',
        'phone',
        'address',
        'city',
        'postal_code',
        'country',
        'tax_id',
        'notes',
        'status',
        'website'
    ];

    /**
     * Relation avec les produits
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Obtenir le nombre total de produits du fournisseur
     */
    public function getProductCountAttribute()
    {
        return $this->products()->count();
    }

    /**
     * Obtenir la valeur totale du stock du fournisseur
     */
    public function getStockValueAttribute()
    {
        return $this->products()
            ->where('type', 'physical')
            ->sum(DB::raw('stock_quantity * cost_price'));
    }

    /**
     * Obtenir les 5 derniers produits du fournisseur
     */
    public function getLatestProductsAttribute()
    {
        return $this->products()->latest()->take(5)->get();
    }
} 
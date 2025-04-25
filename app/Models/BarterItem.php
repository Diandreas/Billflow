<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarterItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'barter_id',
        'product_id',
        'name',
        'description',
        'type',
        'value',
        'quantity',
    ];

    protected $casts = [
        'value' => 'float',
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
     * Relation avec le produit (si associé)
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relation avec les images de l'article
     */
    public function images()
    {
        return $this->hasMany(BarterItemImage::class);
    }

    /**
     * Vérifie si cet article est un article donné par le client
     */
    public function isGivenItem()
    {
        return $this->type === 'given';
    }

    /**
     * Vérifie si cet article est un article reçu par le client
     */
    public function isReceivedItem()
    {
        return $this->type === 'received';
    }

    /**
     * Calcule la valeur totale de cet article
     */
    public function getTotalValue()
    {
        return $this->value * $this->quantity;
    }

    /**
     * Obtient les statistiques de tous les articles de troc
     */
    public static function getGlobalStats()
    {
        // Nombre total d'articles
        $totalItems = self::count();

        // Valeur totale des articles
        $totalValue = self::selectRaw('SUM(value * quantity) as total_value')->first()->total_value;

        // Répartition par type
        $typeDistribution = self::selectRaw('type, count(*) as count, SUM(value * quantity) as total_value')
            ->groupBy('type')
            ->get()
            ->keyBy('type')
            ->toArray();

        // Articles avec produits associés
        $withProducts = self::whereNotNull('product_id')->count();

        // Articles sans produits associés
        $withoutProducts = self::whereNull('product_id')->count();

        return [
            'total_items' => $totalItems,
            'total_value' => $totalValue,
            'type_distribution' => $typeDistribution,
            'with_products' => $withProducts,
            'without_products' => $withoutProducts,
        ];
    }
}

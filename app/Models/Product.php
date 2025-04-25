<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'default_price',
        'type',
        'sku',
        'stock_quantity',
        'stock_alert_threshold',
        'accounting_category',
        'tax_category',
        'cost_price',
        'status',
        'category_id',
        'is_barterable'
    ];

    protected $casts = [
        'default_price' => 'float',
        'cost_price' => 'float',
        'stock_quantity' => 'integer',
        'stock_alert_threshold' => 'integer',
        'is_barterable' => 'boolean'
    ];

    protected $attributes = [
        'status' => 'actif',
        'stock_quantity' => 0,
        'is_barterable' => false
    ];

    /**
     * Relation avec la catégorie
     */
    public function category()
    {
        return $this->belongsTo(ProductCategory::class);
    }

    /**
     * Relation avec les factures
     */
    public function bills()
    {
        return $this->belongsToMany(Bill::class, 'bill_items')
            ->withPivot('quantity', 'price', 'total')
            ->withTimestamps();
    }

    /**
     * Relation avec les éléments de facture
     */
    public function billItems()
    {
        return $this->hasMany(BillItem::class);
    }

    /**
     * Relation avec les mouvements d'inventaire
     */
    public function inventoryMovements()
    {
        return $this->hasMany(InventoryMovement::class);
    }

    /**
     * Relation avec les éléments de troc
     */
    public function barterItems()
    {
        return $this->hasMany(BarterItem::class);
    }

    /**
     * Vérifie si le produit est en rupture de stock
     */
    public function isOutOfStock()
    {
        return $this->type === 'physical' && $this->stock_quantity <= 0;
    }

    /**
     * Vérifie si le stock est bas
     */
    public function isLowStock()
    {
        return $this->type === 'physical'
            && $this->stock_alert_threshold > 0
            && $this->stock_quantity <= $this->stock_alert_threshold
            && $this->stock_quantity > 0;
    }

    /**
     * Calcule la marge bénéficiaire en pourcentage
     */
    public function getProfitMargin()
    {
        if (!$this->cost_price || $this->cost_price <= 0) {
            return 0;
        }

        return (($this->default_price - $this->cost_price) / $this->cost_price) * 100;
    }

    /**
     * Récupère les statistiques de vente pour ce produit
     */
    public function getSalesStats()
    {
        $billItems = $this->billItems()
            ->join('bills', 'bills.id', '=', 'bill_items.bill_id')
            ->where('bills.status', '!=', 'cancelled')
            ->get();

        $totalQuantity = $billItems->sum('quantity');
        $totalAmount = $billItems->sum(function($item) {
            return $item->quantity * $item->unit_price;
        });

        return [
            'total_quantity' => $totalQuantity,
            'total_amount' => $totalAmount,
            'average_price' => $totalQuantity > 0 ? $totalAmount / $totalQuantity : 0
        ];
    }

    /**
     * Récupère les statistiques combinées (ventes + trocs) pour ce produit
     */
    public function getCombinedStats()
    {
        // Statistiques de vente
        $salesStats = $this->getSalesStats();

        // Statistiques de troc
        $barterStats = Barter::getProductBarterStats($this->id);

        // Combiner les statistiques
        $totalQuantity = $salesStats['total_quantity'] + $barterStats['total_quantity'];
        $totalValue = $salesStats['total_amount'] + $barterStats['total_value'];

        $percentageSales = $totalQuantity > 0 ? ($salesStats['total_quantity'] / $totalQuantity) * 100 : 0;
        $percentageBarter = $totalQuantity > 0 ? ($barterStats['total_quantity'] / $totalQuantity) * 100 : 0;

        return [
            'total_quantity' => $totalQuantity,
            'total_value' => $totalValue,
            'average_value' => $totalQuantity > 0 ? $totalValue / $totalQuantity : 0,
            'sales_stats' => $salesStats,
            'barter_stats' => $barterStats,
            'percentage_sales' => $percentageSales,
            'percentage_barter' => $percentageBarter
        ];
    }

    /**
     * Récupère tous les produits disponibles pour le troc
     */
    public static function getBarterableProducts()
    {
        return self::where('is_barterable', true)
            ->where('type', 'physical')
            ->where('stock_quantity', '>', 0)
            ->where('status', 'actif')
            ->get();
    }
}

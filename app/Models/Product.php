<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
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
        'is_barterable',
    ];

    protected $casts = [
        'default_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'stock_alert_threshold' => 'integer',
        'is_barterable' => 'boolean',
    ];

    public function bills()
    {
        return $this->belongsToMany(Bill::class, 'bill_items')
            ->withPivot('quantity', 'unit_price', 'price', 'total')
            ->withTimestamps();
    }

    /**
     * Obtenir la catégorie du produit
     */
    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    /**
     * Obtenir les mouvements de stock pour ce produit
     */
    public function inventoryMovements()
    {
        return $this->hasMany(InventoryMovement::class);
    }

    /**
     * Relation avec les trocs où ce produit a été utilisé
     */
    public function barterItems()
    {
        return $this->hasMany(BarterItem::class);
    }

    public function isLowStock()
    {
        // Les services n'ont pas de stock à gérer
        if ($this->type !== 'physical') return false;
        
        if (!$this->stock_alert_threshold) return false;
        return $this->stock_quantity <= $this->stock_alert_threshold;
    }

    public function isOutOfStock()
    {
        // Les services n'ont pas de stock à gérer
        if ($this->type !== 'physical') return false;
        
        return $this->stock_quantity <= 0;
    }

    public function getProfit()
    {
        return $this->default_price - $this->cost_price;
    }

    public function getProfitMargin()
    {
        if ($this->default_price == 0) return 0;
        return ($this->getProfit() / $this->default_price) * 100;
    }

    /**
     * Vérifie si le produit peut être utilisé dans un troc
     */
    public function isBarterable()
    {
        return $this->is_barterable && $this->type === 'physical';
    }
    
    /**
     * Scope pour les produits qui peuvent être utilisés dans un troc
     */
    public function scopeBarterable($query)
    {
        return $query->where('is_barterable', true)->where('type', 'physical');
    }
}

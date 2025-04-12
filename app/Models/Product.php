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
    ];

    protected $casts = [
        'default_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'stock_alert_threshold' => 'integer',
    ];

    public function bills()
    {
        return $this->belongsToMany(Bill::class, 'bill_products')
            ->withPivot('unit_price', 'quantity', 'total')
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

    public function isLowStock()
    {
        if (!$this->stock_alert_threshold) return false;
        return $this->stock_quantity <= $this->stock_alert_threshold;
    }

    public function isOutOfStock()
    {
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
}

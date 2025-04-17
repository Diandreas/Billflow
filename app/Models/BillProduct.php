<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillProduct extends Model
{
    protected $table = 'bill_products';
    
    protected $fillable = [
        'bill_id',
        'product_id',
        'unit_price',
        'quantity',
        'total'
    ];
    
    protected $casts = [
        'unit_price' => 'decimal:2',
        'quantity' => 'integer',
        'total' => 'decimal:2',
    ];
    
    /**
     * Relation avec la facture associée
     */
    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }
    
    /**
     * Relation avec le produit associé
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

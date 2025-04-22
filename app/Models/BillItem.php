<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillItem extends Model
{
    use HasFactory;
    
    protected $table = 'bill_items';
    
    protected $fillable = [
        'bill_id',
        'product_id',
        'unit_price',
        'quantity',
        'price',
        'total',
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

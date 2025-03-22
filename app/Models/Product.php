<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'default_price',
    ];

    protected $casts = [
        'default_price' => 'decimal:2',
    ];

    public function bills()
    {
        return $this->belongsToMany(Bill::class, 'bill_products')
            ->withPivot('unit_price', 'quantity', 'total')
            ->withTimestamps();
    }
}

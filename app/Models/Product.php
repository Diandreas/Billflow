<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    public function bills()
    {
        return $this->belongsToMany(Bill::class, 'bill_products')
            ->withPivot('unit_price', 'quantity', 'total')
            ->withTimestamps();
    }
}

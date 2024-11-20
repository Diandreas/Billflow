<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    protected $fillable = [
        'reference',
        'description',
        'total',
        'date',
        'tax_rate',
        'tax_amount',
        'user_id',
        'client_id'
    ];

    protected $casts = [
        'date' => 'date',
        'total' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'bill_products')
            ->withPivot('unit_price', 'quantity', 'total')
            ->withTimestamps();
    }
    // Méthode pour générer une référence unique
    public static function generateReference()
    {
        $prefix = 'FACT-';
        $year = date('Y');
        $lastBill = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $number = $lastBill ? intval(substr($lastBill->reference, -5)) + 1 : 1;

        return $prefix . $year . '-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    // Méthode pour calculer les totaux
    public function calculateTotals()
    {
        $subtotal = 0;
        foreach ($this->products as $product) {
            $subtotal += $product->pivot->unit_price * $product->pivot->quantity;
        }

        $this->total = $subtotal;
        $this->tax_amount = $subtotal * ($this->tax_rate / 100);
        $this->total += $this->tax_amount;

        $this->save();
    }
}

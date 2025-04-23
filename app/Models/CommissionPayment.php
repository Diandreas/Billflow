<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommissionPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'shop_id',
        'user_id',
        'paid_by',
        'amount',
        'payment_method',
        'payment_reference',
        'notes',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    /**
     * Génère une référence unique pour le paiement
     */
    public static function generateReference()
    {
        $prefix = 'COM-PAY-';
        $year = date('Y');
        $lastPayment = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $number = $lastPayment ? intval(substr($lastPayment->reference, -5)) + 1 : 1;

        return $prefix . $year . '-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Relation avec le vendeur concerné
     */
    public function vendor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relation avec l'utilisateur qui a effectué le paiement
     */
    public function paidByUser()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    /**
     * Relation avec la boutique concernée
     */
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * Relation avec les commissions associées à ce paiement
     */
    public function commissions()
    {
        return $this->hasMany(Commission::class, 'payment_group_id');
    }
}

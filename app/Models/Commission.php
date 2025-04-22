<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bill_id',
        'shop_id',
        'type',
        'amount',
        'rate',
        'base_amount',
        'description',
        'period_start',
        'period_end',
        'status',
        'paid_at',
        'paid_by',
        'payment_method',
        'payment_reference'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'rate' => 'decimal:2',
        'base_amount' => 'decimal:2',
        'period_start' => 'date',
        'period_end' => 'date',
        'paid_at' => 'datetime'
    ];

    /**
     * Relation avec le vendeur qui reçoit la commission
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec la facture liée à cette commission
     */
    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    /**
     * Relation avec la boutique où la vente a été effectuée
     */
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * Relation avec l'utilisateur qui a effectué le paiement
     */
    public function paidByUser()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    /**
     * Calculer le montant de la commission pour une vente
     */
    public static function calculateForBill(Bill $bill)
    {
        // Si pas de vendeur défini, pas de commission
        if (!$bill->seller_id) {
            return null;
        }

        $seller = User::find($bill->seller_id);
        if (!$seller || !$seller->commission_rate) {
            return null;
        }

        // Calculer la commission de base (sur le montant total)
        $commission = new Commission();
        $commission->user_id = $seller->id;
        $commission->bill_id = $bill->id;
        $commission->shop_id = $bill->shop_id;
        $commission->type = 'vente';
        $commission->base_amount = $bill->total;
        $commission->rate = $seller->commission_rate;
        $commission->amount = $bill->total * ($seller->commission_rate / 100);
        $commission->description = "Commission sur la facture {$bill->reference}";
        $commission->status = 'pending';
        $commission->save();

        return $commission;
    }

    /**
     * Scope pour les commissions en attente
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope pour les commissions approuvées
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope pour les commissions payées
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope pour les commissions d'une période spécifique
     */
    public function scopeForPeriod($query, $start, $end)
    {
        return $query->whereBetween('created_at', [$start, $end]);
    }

    /**
     * Scope pour les commissions d'un utilisateur spécifique
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope pour les commissions d'une boutique spécifique
     */
    public function scopeForShop($query, $shopId)
    {
        return $query->where('shop_id', $shopId);
    }
} 
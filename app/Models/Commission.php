<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
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
        'is_paid',
        'paid_at',
        'paid_by',
        'payment_method',
        'payment_reference',
        'payment_group_id',
        'payment_notes'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'rate' => 'decimal:2',
        'base_amount' => 'decimal:2',
        'period_start' => 'date',
        'period_end' => 'date',
        'paid_at' => 'datetime',
        'is_paid' => 'boolean'
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
     * Relation avec le groupe de paiement
     */
    public function payment()
    {
        return $this->belongsTo(CommissionPayment::class, 'payment_group_id');
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

        // Calculer la base de commission (profit total sur la facture)
        $baseAmount = 0;
        $totalProfit = 0;

        foreach ($bill->items as $item) {
            if ($item->product && $item->product->type === 'physical' && $item->product->cost_price > 0) {
                // Pour les produits physiques avec un prix d'achat, calculer sur la marge
                $unitProfit = $item->unit_price - $item->product->cost_price;
                $itemProfit = $unitProfit * $item->quantity;
                $totalProfit += $itemProfit > 0 ? $itemProfit : 0;
            } else {
                // Pour les services ou produits sans prix d'achat, calculer sur le montant total
                $baseAmount += $item->total;
            }
        }

        // La base de calcul est la somme du profit sur les produits physiques et du montant total des services
        $commissionBase = $totalProfit + $baseAmount;

        // Créer la commission
        $commission = new Commission();
        $commission->user_id = $seller->id;
        $commission->bill_id = $bill->id;
        $commission->shop_id = $bill->shop_id;
        $commission->type = 'vente';
        $commission->base_amount = $commissionBase;
        $commission->rate = $seller->commission_rate;
        $commission->amount = $commissionBase * ($seller->commission_rate / 100);
        $commission->description = "Commission sur la facture {$bill->reference}";
        $commission->is_paid = false;
        $commission->save();

        return $commission;
    }

    /**
     * Scope pour les commissions en attente
     */
    public function scopePending($query)
    {
        return $query->where('is_paid', false);
    }

    /**
     * Scope pour les commissions approuvées
     */
    public function scopeApproved($query)
    {
        return $query->where('is_paid', true);
    }

    /**
     * Scope pour les commissions payées
     */
    public function scopePaid($query)
    {
        return $query->where('is_paid', true);
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

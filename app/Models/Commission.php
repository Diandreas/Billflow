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
        'barter_id',
        'shop_id',
        'amount',
        'rate',
        'base_amount',
        'type',
        'description',
        'is_paid',
        'paid_at',
        'paid_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'rate' => 'decimal:2',
        'base_amount' => 'decimal:2',
        'is_paid' => 'boolean',
        'paid_at' => 'date',
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
     * Relation avec le troc lié à cette commission
     */
    public function barter()
    {
        return $this->belongsTo(Barter::class);
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
    public function paidBy()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    /**
     * Scope pour les commissions non payées
     */
    public function scopeUnpaid($query)
    {
        return $query->where('is_paid', false);
    }

    /**
     * Scope pour les commissions payées
     */
    public function scopePaid($query)
    {
        return $query->where('is_paid', true);
    }

    /**
     * Scope pour les commissions d'un type spécifique
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Calculer le montant de la commission pour une vente
     */
    public static function calculateForSale(Bill $bill, $rateOverride = null)
    {
        $seller = User::find($bill->seller_id);
        
        if (!$seller) {
            return null;
        }
        
        // Utiliser le taux personnalisé s'il est fourni, sinon utiliser celui du vendeur
        $rate = $rateOverride ?? $seller->commission_rate;
        
        // Aucune commission si le taux est nul
        if ($rate <= 0) {
            return null;
        }
        
        $amount = $bill->total * ($rate / 100);
        
        return self::create([
            'user_id' => $seller->id,
            'bill_id' => $bill->id,
            'shop_id' => $bill->shop_id,
            'amount' => $amount,
            'rate' => $rate,
            'base_amount' => $bill->total,
            'type' => 'vente',
            'description' => 'Commission sur la vente ' . $bill->reference,
            'is_paid' => false,
        ]);
    }

    /**
     * Marquer la commission comme payée
     */
    public function markAsPaid($paidBy = null)
    {
        $this->update([
            'is_paid' => true,
            'paid_at' => now(),
            'paid_by' => $paidBy,
        ]);
        
        return $this;
    }
} 
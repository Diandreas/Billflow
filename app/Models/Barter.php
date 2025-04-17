<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barter extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'client_id',
        'shop_id',
        'user_id',
        'seller_id',
        'given_items_value',
        'received_items_value',
        'balance_amount',
        'status',
        'date',
        'notes',
        'signature_path',
    ];

    protected $casts = [
        'given_items_value' => 'decimal:2',
        'received_items_value' => 'decimal:2',
        'balance_amount' => 'decimal:2',
        'date' => 'date',
    ];

    /**
     * Génère une référence unique pour le troc
     */
    public static function generateReference()
    {
        return 'TROC-' . date('YmdHis') . '-' . rand(100, 999);
    }

    /**
     * Relation avec le client
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Relation avec la boutique
     */
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * Relation avec l'utilisateur qui a enregistré le troc
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec le vendeur qui a effectué le troc
     */
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * Relation avec les articles donnés par le client
     */
    public function givenItems()
    {
        return $this->hasMany(BarterGivenItem::class);
    }

    /**
     * Relation avec les articles reçus par le client
     */
    public function receivedItems()
    {
        return $this->hasMany(BarterReceivedItem::class);
    }

    /**
     * Relation avec la commission liée à ce troc
     */
    public function commission()
    {
        return $this->hasOne(Commission::class);
    }

    /**
     * Relation avec la livraison liée à ce troc (si applicable)
     */
    public function delivery()
    {
        return $this->hasOne(Delivery::class);
    }

    /**
     * Scope pour les trocs en attente
     */
    public function scopePending($query)
    {
        return $query->where('status', 'en_attente');
    }

    /**
     * Scope pour les trocs complétés
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'complété');
    }

    /**
     * Scope pour les trocs annulés
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'annulé');
    }

    /**
     * Scope pour les trocs d'une boutique spécifique
     */
    public function scopeForShop($query, $shopId)
    {
        return $query->where('shop_id', $shopId);
    }

    /**
     * Scope pour les trocs d'un vendeur spécifique
     */
    public function scopeForSeller($query, $sellerId)
    {
        return $query->where('seller_id', $sellerId);
    }

    /**
     * Calcule le total des articles donnés par le client
     */
    public function calculateGivenItemsValue()
    {
        $total = $this->givenItems()->sum(\DB::raw('estimated_value * quantity'));
        $this->update(['given_items_value' => $total]);
        return $total;
    }

    /**
     * Calcule le total des articles reçus par le client
     */
    public function calculateReceivedItemsValue()
    {
        $total = $this->receivedItems()->sum('total_price');
        $this->update(['received_items_value' => $total]);
        return $total;
    }

    /**
     * Calcule le montant d'équilibrage du troc
     * Positif si le client doit payer, négatif si le client doit recevoir
     */
    public function calculateBalanceAmount()
    {
        $balance = $this->received_items_value - $this->given_items_value;
        $this->update(['balance_amount' => $balance]);
        return $balance;
    }

    /**
     * Vérifie si le client doit payer un équilibrage
     */
    public function clientNeedsToPay()
    {
        return $this->balance_amount > 0;
    }

    /**
     * Vérifie si le client doit recevoir un équilibrage
     */
    public function clientNeedsToReceive()
    {
        return $this->balance_amount < 0;
    }

    /**
     * Marque le troc comme complété
     */
    public function markAsCompleted()
    {
        $this->update(['status' => 'complété']);
        return $this;
    }

    /**
     * Marque le troc comme annulé
     */
    public function markAsCancelled()
    {
        $this->update(['status' => 'annulé']);
        return $this;
    }

    /**
     * Crée une commission pour le vendeur basée sur ce troc
     */
    public function createCommission()
    {
        $seller = $this->seller;
        
        if (!$seller || $seller->commission_rate <= 0) {
            return null;
        }
        
        // Calculer le montant de la commission basé sur la valeur des articles reçus ou l'équilibrage
        $baseAmount = $this->clientNeedsToPay() ? $this->balance_amount : $this->received_items_value;
        
        if ($baseAmount <= 0) {
            return null;
        }
        
        $amount = $baseAmount * ($seller->commission_rate / 100);
        
        return Commission::create([
            'user_id' => $seller->id,
            'barter_id' => $this->id,
            'shop_id' => $this->shop_id,
            'amount' => $amount,
            'rate' => $seller->commission_rate,
            'base_amount' => $baseAmount,
            'type' => 'troc',
            'description' => 'Commission sur le troc ' . $this->reference,
            'is_paid' => false,
        ]);
    }
} 
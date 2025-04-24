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
        'user_id',
        'seller_id',
        'shop_id',
        'type',
        'value_given',
        'value_received',
        'additional_payment',
        'payment_method',
        'description',
        'status'
    ];

    protected $casts = [
        'value_given' => 'decimal:2',
        'value_received' => 'decimal:2',
        'additional_payment' => 'decimal:2',
    ];

    /**
     * Génère une référence unique pour le troc
     */
    public static function generateReference()
    {
        $prefix = 'TROC-';
        $year = date('Y');
        $lastBarter = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $number = $lastBarter ? intval(substr($lastBarter->reference, -5)) + 1 : 1;

        return $prefix . $year . '-' . str_pad($number, 5, '0', STR_PAD_LEFT);
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
     * Relation avec les images liées à ce troc
     */
    public function images()
    {
        return $this->hasMany(BarterImage::class);
    }

    /**
     * Relation avec les articles liées à ce troc
     */
    public function items()
    {
        return $this->hasMany(BarterItem::class);
    }

    /**
     * Relation avec les articles donnés par le client
     */
    public function givenItems()
    {
        return $this->items()->where('type', 'given');
    }

    /**
     * Relation avec les articles reçus par le client
     */
    public function receivedItems()
    {
        return $this->items()->where('type', 'received');
    }

    /**
     * Relation avec les images données par le client
     */
    public function givenImages()
    {
        return $this->images()->where('type', 'given');
    }

    /**
     * Relation avec les images reçues par le client
     */
    public function receivedImages()
    {
        return $this->images()->where('type', 'received');
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
        return $query->where('status', 'pending');
    }

    /**
     * Scope pour les trocs complétés
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope pour les trocs annulés
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
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
    public function scopeForUser($query, $userId)
    {
        return $query->where(function($q) use ($userId) {
            $q->where('user_id', $userId)
              ->orWhere('seller_id', $userId);
        });
    }

    /**
     * Calcule le total des articles donnés par le client
     */
    public function calculateGivenItemsValue()
    {
        $total = $this->givenItems()->sum(\DB::raw('estimated_value * quantity'));
        $this->update(['value_given' => $total]);
        return $total;
    }

    /**
     * Calcule le total des articles reçus par le client
     */
    public function calculateReceivedItemsValue()
    {
        $total = $this->receivedItems()->sum('total_price');
        $this->update(['value_received' => $total]);
        return $total;
    }

    /**
     * Calcule le montant d'équilibrage du troc
     * Positif si le client doit payer, négatif si le client doit recevoir
     */
    public function calculateBalanceAmount()
    {
        $balance = $this->value_received - $this->value_given;
        $this->update(['additional_payment' => $balance]);
        return $balance;
    }

    /**
     * Vérifie si le client doit payer un équilibrage
     */
    public function clientNeedsToPay()
    {
        return $this->additional_payment > 0;
    }

    /**
     * Vérifie si le client doit recevoir un équilibrage
     */
    public function clientNeedsToReceive()
    {
        return $this->additional_payment < 0;
    }

    /**
     * Marque le troc comme complété
     */
    public function markAsCompleted()
    {
        $this->update(['status' => 'completed']);
        return $this;
    }

    /**
     * Marque le troc comme annulé
     */
    public function markAsCancelled()
    {
        $this->update(['status' => 'cancelled']);
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
        $baseAmount = $this->clientNeedsToPay() ? $this->additional_payment : $this->value_received;

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

    /**
     * Getter pour le montant d'équilibrage
     */
    public function getBalanceAttribute()
    {
        return $this->value_received - $this->value_given;
    }

    /**
     * Getter pour vérifier si le troc est du même type
     */
    public function getIsSameTypeAttribute()
    {
        return $this->type === 'same_type';
    }

    // Génère automatiquement une facture pour ce troc
    public function generateBill()
    {
        // Vérifier si une facture existe déjà
        if ($this->bill) {
            return $this->bill;
        }

        return DB::transaction(function () {
            // Créer la facture
            $bill = new Bill();
            $bill->reference = 'FACT-TROC-' . $this->reference;
            $bill->client_id = $this->client_id;
            $bill->shop_id = $this->shop_id;
            $bill->seller_id = $this->seller_id;
            $bill->user_id = $this->user_id;
            $bill->date = now();
            $bill->due_date = now()->addDays(30);
            $bill->tax_rate = 0; // Les trocs sont généralement sans TVA
            $bill->tax_amount = 0;
            $bill->total = $this->additional_payment > 0 ? $this->additional_payment : 0;
            $bill->status = 'paid'; // Considéré comme payé dès la création
            $bill->payment_method = $this->payment_method;
            $bill->description = 'Facture automatique pour le troc ' . $this->reference;
            $bill->is_barter_bill = true; // Marquer comme facture de troc
            $bill->save();

            // Associer la facture au troc
            $this->bill_id = $bill->id;
            $this->save();

            // Ajouter les articles à la facture
            // Pour un troc, nous n'ajoutons que les articles avec payment supplémentaire
            if ($this->additional_payment > 0) {
                // Créer un article spécial pour le paiement complémentaire
                $billItem = new BillItem();
                $billItem->bill_id = $bill->id;
                $billItem->product_id = null;
                $billItem->unit_price = $this->additional_payment;
                $billItem->quantity = 1;
                $billItem->total = $this->additional_payment;
                $billItem->name = 'Paiement complémentaire pour troc ' . $this->reference;
                $billItem->save();
            }

            return $bill;
        });
    }

    // Recalculer les valeurs du troc
    public function recalculateValues()
    {
        $this->value_given = $this->givenItems->sum(function ($item) {
            return $item->value * $item->quantity;
        });

        $this->value_received = $this->receivedItems->sum(function ($item) {
            return $item->value * $item->quantity;
        });

        $this->additional_payment = $this->value_received - $this->value_given;
        $this->save();

        return $this;
    }

    // Obtenir les statistiques des produits provenant de trocs
    public static function getProductBarterStats($productId)
    {
        $product = Product::findOrFail($productId);

        // Nombre total de trocs où ce produit a été utilisé
        $totalBarters = BarterItem::where('product_id', $productId)->count();

        // Nombre de trocs où ce produit a été reçu par le client
        $receivedBarters = BarterItem::where('product_id', $productId)
            ->where('type', 'received')
            ->count();

        // Nombre de trocs où ce produit a été donné par le client
        $givenBarters = BarterItem::where('product_id', $productId)
            ->where('type', 'given')
            ->count();

        // Quantité totale échangée
        $totalQuantity = BarterItem::where('product_id', $productId)->sum('quantity');

        // Valeur totale échangée
        $totalValue = BarterItem::where('product_id', $productId)
            ->selectRaw('SUM(value * quantity) as total_value')
            ->first()
            ->total_value ?? 0;

        return [
            'total_barters' => $totalBarters,
            'received_barters' => $receivedBarters,
            'given_barters' => $givenBarters,
            'total_quantity' => $totalQuantity,
            'total_value' => $totalValue,
            'average_value' => $totalQuantity > 0 ? $totalValue / $totalQuantity : 0,
        ];
    }
}

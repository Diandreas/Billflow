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
        'due_date',
        'tax_rate',
        'tax_amount',
        'user_id',
        'client_id',
        'status',
        'barter_id',
        'shop_id'
    ];

    protected $casts = [
        'date' => 'datetime',
        'due_date' => 'datetime',
        'total' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
    ];

    protected $attributes = [
        'status' => 'pending'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'bill_items')
            ->withPivot('quantity', 'unit_price', 'price', 'total')
            ->withTimestamps();
    }

    /**
     * Relation avec les produits de la facture via le modèle BillItem
     */
    public function items()
    {
        return $this->hasMany(BillItem::class);
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
    /**
     * Relation avec le troc associé à cette facture
     */
    public function barter()
    {
        return $this->belongsTo(Barter::class);
    }

    /**
     * Détermine si cette facture est liée à un troc
     */
    public function isBarterBill()
    {
        return $this->is_barter_bill;
    }

    /**
     * Crée une facture pour un troc spécifique
     */
    public static function createForBarter(Barter $barter, $additionalPayment = 0)
    {
        $bill = new self();
        $bill->reference = 'FACT-TROC-' . $barter->reference;
        $bill->client_id = $barter->client_id;
        $bill->shop_id = $barter->shop_id;
        $bill->seller_id = $barter->seller_id;
        $bill->user_id = $barter->user_id ?? auth()->id();
        $bill->date = now();
        $bill->due_date = now()->addDays(30);
        $bill->tax_rate = 0; // Les trocs sont généralement sans TVA
        $bill->tax_amount = 0;
        $bill->total = $additionalPayment > 0 ? $additionalPayment : 0;
        $bill->status = 'paid'; // Considéré comme payé dès la création
        $bill->payment_method = $barter->payment_method;
        $bill->description = 'Facture pour le troc ' . $barter->reference;
        $bill->is_barter_bill = true;
        $bill->barter_id = $barter->id;
        $bill->save();

        return $bill;
    }
    public function formatAmount($amount)
    {
        return number_format($amount, 0, ',', ' ') . ' FCFA';
    }
}

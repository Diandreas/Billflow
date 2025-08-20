<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Quote extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'description',
        'total',
        'date',
        'valid_until',
        'tax_rate',
        'tax_amount',
        'user_id',
        'client_id',
        'status',
        'shop_id',
        'bill_id',
        'notes',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'date' => 'date',
        'valid_until' => 'date',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function bill(): BelongsTo
    {
        return $this->belongsTo(Bill::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuoteItem::class);
    }

    /**
     * Générer une référence unique pour le devis
     */
    public static function generateReference(): string
    {
        $prefix = 'DEV-';
        $year = date('Y');
        $lastQuote = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $number = $lastQuote ? intval(substr($lastQuote->reference, -5)) + 1 : 1;

        return $prefix . $year . '-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Calculer les totaux du devis
     */
    public function calculateTotals(): void
    {
        $subtotal = $this->items->sum(function ($item) {
            return $item->quantity * $item->unit_price;
        });

        $this->tax_amount = $subtotal * ($this->tax_rate / 100);
        $this->total = $subtotal + $this->tax_amount;
        $this->save();
    }

    /**
     * Convertir le devis en facture
     */
    public function convertToBill(): Bill
    {
        $bill = Bill::create([
            'reference' => Bill::generateReference(),
            'client_id' => $this->client_id,
            'shop_id' => $this->shop_id,
            'user_id' => $this->user_id,
            'date' => now(),
            'due_date' => now()->addDays(30),
            'tax_rate' => $this->tax_rate,
            'tax_amount' => $this->tax_amount,
            'total' => $this->total,
            'status' => 'pending',
            'quote_id' => $this->id,
            'description' => $this->description,
        ]);

        // Copier les items du devis vers la facture
        foreach ($this->items as $quoteItem) {
            $bill->items()->create([
                'product_id' => $quoteItem->product_id,
                'quantity' => $quoteItem->quantity,
                'unit_price' => $quoteItem->unit_price,
                'price' => $quoteItem->price,
                'total' => $quoteItem->total,
            ]);
        }

        // Mettre à jour le statut du devis
        $this->update([
            'status' => 'converted',
            'bill_id' => $bill->id,
        ]);

        return $bill;
    }

    /**
     * Vérifier si le devis est expiré
     */
    public function isExpired(): bool
    {
        return $this->valid_until < now();
    }

    /**
     * Formater le montant
     */
    public function formatAmount(): string
    {
        return number_format($this->total, 0, ',', ' ') . ' FCFA';
    }
}
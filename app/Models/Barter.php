<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Bill;
use Illuminate\Support\Facades\DB;

class Barter extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'client_id',
        'shop_id',
        'user_id',
        'seller_id',
        'type',
        'value_given',
        'value_received',
        'additional_payment',
        'payment_method',
        'notes',
        'status',
        'description',
        'bill_id',
    ];

    protected $casts = [
        'value_given' => 'float',
        'value_received' => 'float',
        'additional_payment' => 'float',
    ];

    protected $attributes = [
        'status' => 'pending',
    ];

    // Relation vers le client
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // Relation vers l'utilisateur qui a créé le troc
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relation vers le vendeur
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    // Relation vers la boutique
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    // Relation vers les articles
    public function items()
    {
        return $this->hasMany(BarterItem::class);
    }

    // Relation vers les images
    public function images()
    {
        return $this->hasMany(BarterImage::class);
    }

    // Relation vers la facture générée pour ce troc
    public function bill()
    {
        return $this->hasOne(Bill::class);
    }

    // Articles donnés par le client
    public function givenItems()
    {
        return $this->hasMany(BarterItem::class)->where('type', 'given');
    }

    // Articles reçus par le client
    public function receivedItems()
    {
        return $this->hasMany(BarterItem::class)->where('type', 'received');
    }

    // Génère une référence unique pour le troc
    public static function generateReference()
    {
        $lastBarter = self::latest()->first();
        $lastId = $lastBarter ? $lastBarter->id : 0;
        $nextId = $lastId + 1;
        return 'TROC-' . date('Ymd') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
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

            // Utiliser la valeur absolue du paiement complémentaire pour le total
            $bill->total = abs($this->additional_payment);

            $bill->status = 'paid'; // Considéré comme payé dès la création
            $bill->payment_method = $this->payment_method;
            $bill->description = 'Facture automatique pour le troc ' . $this->reference;
            $bill->is_barter_bill = true; // Marquer comme facture de troc
            $bill->barter_id = $this->id;
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
                $billItem->unit_price = abs($this->additional_payment);
                $billItem->quantity = 1;
                $billItem->total = abs($this->additional_payment);
                $billItem->name = 'Paiement complémentaire pour troc ' . $this->reference;
                // Ajouter une indication de la direction du paiement
                $billItem->save();
            }

            // Ajouter également les produits échangés dans la facture pour référence
            foreach ($this->receivedItems as $item) {
                if ($item->product_id) {
                    $billItem = new BillItem();
                    $billItem->bill_id = $bill->id;
                    $billItem->product_id = $item->product_id;
                    $billItem->unit_price = 0; // Prix à 0 car déjà comptabilisé dans le troc
                    $billItem->quantity = $item->quantity;
                    $billItem->total = 0;
                    $billItem->name = 'Produit échangé: ' . $item->name;
                    $billItem->is_barter_item = true;
                    $billItem->save();
                }
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

    // Obtenir les statistiques générales des trocs
    public static function getGlobalStats()
    {
        // Total des trocs
        $totalCount = self::count();

        // Valeur totale échangée
        $totalGivenValue = self::sum('value_given');
        $totalReceivedValue = self::sum('value_received');

        // Regroupement par statut
        $statusCounts = self::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Trocs par mois
        $bartersByMonth = self::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();

        // Produits les plus échangés
        $topProducts = BarterItem::selectRaw('product_id, COUNT(*) as usage_count, SUM(quantity) as total_quantity')
            ->whereNotNull('product_id')
            ->groupBy('product_id')
            ->orderByDesc('usage_count')
            ->limit(10)
            ->with('product')
            ->get();

        return [
            'total_count' => $totalCount,
            'total_given_value' => $totalGivenValue,
            'total_received_value' => $totalReceivedValue,
            'additional_payments' => self::where('additional_payment', '>', 0)->sum('additional_payment'),
            'status_counts' => $statusCounts,
            'by_month' => $bartersByMonth,
            'top_products' => $topProducts
        ];
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

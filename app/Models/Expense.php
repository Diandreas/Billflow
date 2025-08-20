<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'date',
        'amount',
        'category',
        'supplier',
        'payment_method',
        'description',
        'receipt_path',
        'receipt_data',
        'shop_id',
        'user_id',
        'is_recurring',
        'recurring_frequency',
        'next_occurrence',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
        'receipt_data' => 'array',
        'is_recurring' => 'boolean',
        'next_occurrence' => 'date',
    ];

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Générer une référence unique pour la dépense
     */
    public static function generateReference(): string
    {
        $prefix = 'DEP-';
        $year = date('Y');
        $lastExpense = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $number = $lastExpense ? intval(substr($lastExpense->reference, -5)) + 1 : 1;

        return $prefix . $year . '-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Formater le montant
     */
    public function formatAmount(): string
    {
        return number_format($this->amount, 0, ',', ' ') . ' FCFA';
    }

    /**
     * Catégories de dépenses par défaut
     */
    public static function getDefaultCategories(): array
    {
        return [
            'carburant' => 'Carburant',
            'loyer' => 'Loyer',
            'achat_stock' => 'Achat de stock',
            'electricite' => 'Électricité',
            'eau' => 'Eau',
            'internet' => 'Internet/Téléphone',
            'transport' => 'Transport',
            'marketing' => 'Marketing/Publicité',
            'fournitures' => 'Fournitures de bureau',
            'maintenance' => 'Maintenance/Réparation',
            'assurance' => 'Assurance',
            'taxes' => 'Taxes et impôts',
            'salaires' => 'Salaires',
            'formation' => 'Formation',
            'autres' => 'Autres',
        ];
    }

    /**
     * Catégories spécifiques par type de business
     */
    public static function getCategoriesByBusinessType(string $businessType): array
    {
        $baseCategories = self::getDefaultCategories();
        
        switch ($businessType) {
            case 'restaurant':
                return array_merge($baseCategories, [
                    'ingredients' => 'Ingrédients',
                    'vaisselle' => 'Vaisselle/Équipement',
                    'hygiene' => 'Produits d\'hygiène',
                ]);
            
            case 'eleveur':
                return array_merge($baseCategories, [
                    'aliments' => 'Aliments pour animaux',
                    'veterinaire' => 'Frais vétérinaires',
                    'medicaments' => 'Médicaments',
                    'equipement_elevage' => 'Équipement d\'élevage',
                ]);
            
            case 'agriculteur':
                return array_merge($baseCategories, [
                    'semences' => 'Semences',
                    'engrais' => 'Engrais',
                    'pesticides' => 'Pesticides',
                    'equipement_agricole' => 'Équipement agricole',
                ]);
            
            case 'transport':
                return array_merge($baseCategories, [
                    'pieces_detachees' => 'Pièces détachées',
                    'reparations' => 'Réparations véhicules',
                    'peages' => 'Péages',
                    'parkings' => 'Parkings',
                ]);
            
            default:
                return $baseCategories;
        }
    }
}
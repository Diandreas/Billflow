<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'description',
        'logo_path',
        'is_active',
        'business_type',
        'profile_config',
        'active_modules',
        'custom_labels',
        'custom_units',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'profile_config' => 'array',
        'active_modules' => 'array',
        'custom_labels' => 'array',
        'custom_units' => 'array',
    ];

    /**
     * Relation avec les utilisateurs (vendeurs et managers) de la boutique
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'shop_user')
            ->withPivot('is_manager', 'custom_commission_rate', 'assigned_at')
            ->withTimestamps();
    }

    /**
     * Relation avec les managers de la boutique
     */
    public function managers()
    {
        return $this->belongsToMany(User::class, 'shop_user')
            ->wherePivot('is_manager', true)
            ->withPivot('custom_commission_rate', 'assigned_at')
            ->withTimestamps();
    }

    /**
     * Relation avec les vendeurs de la boutique
     */
    public function vendors()
    {
        return $this->belongsToMany(User::class, 'shop_user')
            ->where('role', 'vendeur')
            ->withPivot('is_manager', 'custom_commission_rate', 'assigned_at')
            ->withTimestamps();
    }

    /**
     * Relation avec les factures de la boutique
     */
    public function bills()
    {
        return $this->hasMany(Bill::class);
    }

    /**
     * Relation avec les trocs effectués dans la boutique
     */
    public function barters()
    {
        return $this->hasMany(Barter::class);
    }

    /**
     * Récupère les ventes de la boutique pour une période donnée
     */
    public function salesBetween($startDate, $endDate)
    {
        return $this->bills()
            ->whereBetween('date', [$startDate, $endDate])
            ->where('status', '<>', 'cancelled')
            ->sum('total');
    }

    /**
     * Récupère les ventes du jour
     */
    public function salesToday()
    {
        return $this->salesBetween(now()->startOfDay(), now()->endOfDay());
    }

    /**
     * Récupère les ventes de la semaine
     */
    public function salesThisWeek()
    {
        return $this->salesBetween(now()->startOfWeek(), now()->endOfWeek());
    }

    /**
     * Récupère les ventes du mois
     */
    public function salesThisMonth()
    {
        return $this->salesBetween(now()->startOfMonth(), now()->endOfMonth());
    }

    /**
     * Relation avec les dépenses de la boutique
     */
    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    /**
     * Relation avec les devis de la boutique
     */
    public function quotes()
    {
        return $this->hasMany(Quote::class);
    }

    /**
     * Relation avec les paiements de la boutique
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Obtenir la configuration du profil métier
     */
    public function getBusinessProfile()
    {
        return $this->getBusinessProfiles()[$this->business_type] ?? $this->getBusinessProfiles()['boutique'];
    }

    /**
     * Profils métier disponibles
     */
    public static function getBusinessProfiles()
    {
        return [
            'boutique' => [
                'name' => 'Boutique',
                'description' => 'Commerce de détail',
                'labels' => [
                    'bill' => 'Ticket',
                    'product' => 'Article',
                    'sale' => 'Vente',
                ],
                'units' => ['pièce', 'unité', 'lot'],
                'default_modules' => ['bills', 'products', 'clients', 'expenses', 'payments'],
                'expense_categories' => ['achat_stock', 'loyer', 'electricite', 'eau', 'internet'],
            ],
            'prestataire' => [
                'name' => 'Prestataire de services',
                'description' => 'Services professionnels',
                'labels' => [
                    'bill' => 'Facture',
                    'product' => 'Prestation',
                    'sale' => 'Prestation',
                ],
                'units' => ['heure', 'jour', 'forfait'],
                'default_modules' => ['quotes', 'bills', 'clients', 'expenses', 'payments'],
                'expense_categories' => ['transport', 'internet', 'formation', 'equipement'],
            ],
            'restaurant' => [
                'name' => 'Restaurant',
                'description' => 'Restauration',
                'labels' => [
                    'bill' => 'Addition',
                    'product' => 'Plat',
                    'sale' => 'Commande',
                ],
                'units' => ['portion', 'assiette', 'menu'],
                'default_modules' => ['bills', 'products', 'expenses', 'payments'],
                'expense_categories' => ['ingredients', 'vaisselle', 'hygiene', 'loyer'],
            ],
            'eleveur' => [
                'name' => 'Éleveur',
                'description' => 'Élevage d\'animaux',
                'labels' => [
                    'bill' => 'Vente',
                    'product' => 'Animal/Produit',
                    'sale' => 'Vente',
                ],
                'units' => ['tête', 'litre', 'kg'],
                'default_modules' => ['bills', 'products', 'expenses', 'payments'],
                'expense_categories' => ['aliments', 'veterinaire', 'medicaments', 'equipement_elevage'],
            ],
            'agriculteur' => [
                'name' => 'Agriculteur',
                'description' => 'Agriculture',
                'labels' => [
                    'bill' => 'Vente',
                    'product' => 'Récolte',
                    'sale' => 'Vente',
                ],
                'units' => ['kg', 'tonne', 'sac'],
                'default_modules' => ['bills', 'products', 'expenses', 'payments'],
                'expense_categories' => ['semences', 'engrais', 'pesticides', 'equipement_agricole'],
            ],
            'transport' => [
                'name' => 'Transport',
                'description' => 'Transport de personnes ou marchandises',
                'labels' => [
                    'bill' => 'Course',
                    'product' => 'Trajet',
                    'sale' => 'Course',
                ],
                'units' => ['km', 'course', 'forfait'],
                'default_modules' => ['bills', 'expenses', 'payments'],
                'expense_categories' => ['carburant', 'pieces_detachees', 'reparations', 'peages'],
            ],
        ];
    }

    /**
     * Obtenir les dépenses de la période
     */
    public function expensesBetween($startDate, $endDate)
    {
        return $this->expenses()
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('amount');
    }

    /**
     * Obtenir le résultat (recettes - dépenses) de la période
     */
    public function resultBetween($startDate, $endDate)
    {
        $sales = $this->salesBetween($startDate, $endDate);
        $expenses = $this->expensesBetween($startDate, $endDate);
        return $sales - $expenses;
    }
} 
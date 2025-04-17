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
    ];

    protected $casts = [
        'is_active' => 'boolean',
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
} 
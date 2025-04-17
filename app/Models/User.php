<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'commission_rate',
        'photo_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'commission_rate' => 'decimal:2',
        ];
    }

    /**
     * Relation avec les boutiques où l'utilisateur est vendeur ou manager
     */
    public function shops()
    {
        return $this->belongsToMany(Shop::class, 'shop_user')
            ->withPivot('is_manager')
            ->withTimestamps();
    }

    /**
     * Relation avec les boutiques où l'utilisateur est manager
     */
    public function managedShops()
    {
        return $this->belongsToMany(Shop::class, 'shop_user')
            ->wherePivot('is_manager', true)
            ->withTimestamps();
    }

    /**
     * Relation avec les factures créées par l'utilisateur
     */
    public function bills()
    {
        return $this->hasMany(Bill::class);
    }

    /**
     * Relation avec les factures où l'utilisateur est vendeur
     */
    public function sales()
    {
        return $this->hasMany(Bill::class, 'seller_id');
    }

    /**
     * Relation avec les commissions de l'utilisateur
     */
    public function commissions()
    {
        return $this->hasMany(Commission::class);
    }

    /**
     * Relation avec les trocs où l'utilisateur est vendeur
     */
    public function barters()
    {
        return $this->hasMany(Barter::class, 'seller_id');
    }

    /**
     * Relation avec les livraisons assignées à l'utilisateur
     */
    public function deliveries()
    {
        return $this->hasMany(Delivery::class, 'delivery_agent_id');
    }

    /**
     * Relation avec l'équipement assigné à l'utilisateur
     */
    public function equipment()
    {
        return $this->hasMany(VendorEquipment::class);
    }

    /**
     * Relation avec l'équipement actif (non retourné) assigné à l'utilisateur
     */
    public function activeEquipment()
    {
        return $this->equipment()->active();
    }

    public function campaigns()
    {
        return $this->hasMany(Campaign::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function promotionalMessages()
    {
        return $this->hasMany(PromotionalMessage::class);
    }

    /**
     * Vérifie si l'utilisateur est administrateur
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Vérifie si l'utilisateur est manager
     */
    public function isManager()
    {
        return $this->role === 'manager';
    }

    /**
     * Vérifie si l'utilisateur est vendeur
     */
    public function isVendeur()
    {
        return $this->role === 'vendeur';
    }

    /**
     * Vérifie si l'utilisateur a accès à une boutique spécifique
     */
    public function canAccessShop($shopId)
    {
        if ($this->isAdmin()) {
            return true;
        }
        
        return $this->shops()->where('shops.id', $shopId)->exists();
    }

    /**
     * Vérifie si l'utilisateur peut gérer une boutique spécifique
     */
    public function canManageShop($shopId)
    {
        if ($this->isAdmin()) {
            return true;
        }
        
        return $this->managedShops()->where('shops.id', $shopId)->exists();
    }

    public function getActiveSubscriptionAttribute()
    {
        return $this->subscriptions()
            ->where('status', 'active')
            ->where('ends_at', '>', now())
            ->latest()
            ->first();
    }

    public function hasActiveSubscription()
    {
        return $this->activeSubscription !== null;
    }
}

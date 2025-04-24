<?php

namespace App\Providers;

use App\Models\Bill;
use App\Policies\BillPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Bill::class => BillPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Définir des gates de rôles
        Gate::define('admin', function ($user) {
            return $user->role === 'admin';
        });

        Gate::define('manager', function ($user) {
            return $user->role === 'admin' || $user->role === 'manager';
        });

        Gate::define('vendeur', function ($user) {
            return $user->role === 'vendeur';
        });

        // Gates pour les accès aux boutiques
        Gate::define('access-shop', function ($user, $shop) {
            return $user->shops()->where('shops.id', $shop->id)->exists() || $user->role === 'admin';
        });

        Gate::define('manage-shop', function ($user, $shop) {
            return $user->role === 'admin' || 
                   $user->shops()
                        ->where('shops.id', $shop->id)
                        ->wherePivot('is_manager', true)
                        ->exists();
        });

        // Gates pour les accès aux fonctionnalités
        Gate::define('view-dashboard', function ($user) {
            return in_array($user->role, ['admin', 'manager']);
        });

        Gate::define('manage-users', function ($user) {
            return $user->role === 'admin';
        });

        Gate::define('manage-products', function ($user) {
            return in_array($user->role, ['admin', 'manager', 'vendeur']);
        });

        Gate::define('create-bill', function ($user) {
            return true; // Tous les utilisateurs authentifiés peuvent créer des factures
        });

        Gate::define('edit-bill', function ($user, $bill) {
            // Seuls les admins peuvent modifier les factures
            return $user->role === 'admin';
        });

        Gate::define('delete-bill', function ($user, $bill) {
            // Seuls les admins peuvent supprimer des factures
            return $user->role === 'admin';
        });
        
        // Gates pour les impressions de factures
        Gate::define('print-bill', function ($user, $bill) {
            return $user->role === 'admin' || 
                   $user->id === $bill->user_id || 
                   $user->id === $bill->seller_id || 
                   $user->shops()->where('shops.id', $bill->shop_id)->exists();
        });
        
        // Gates pour l'approbation des factures
        Gate::define('approve-bill', function ($user) {
            return in_array($user->role, ['admin', 'manager']);
        });
        
        // Gates pour les commissions
        Gate::define('view-commissions', function ($user) {
            return in_array($user->role, ['admin', 'manager']);
        });
        
        Gate::define('view-commission', function ($user, $commission) {
            if ($user->role === 'admin') {
                return true;
            }
            
            if ($user->role === 'manager') {
                return $user->shops()->where('shops.id', $commission->shop_id)->exists();
            }
            
            return $user->id === $commission->user_id;
        });
        
        Gate::define('view-shop-report', function ($user, $shop) {
            // Les admins ont toujours accès
            if ($user->role === 'admin') {
                return true;
            }
            
            // Les managers ont accès si ils gèrent cette boutique
            if ($user->role === 'manager') {
                return $user->shops()
                    ->where('shops.id', $shop->id)
                    ->wherePivot('is_manager', true)
                    ->exists();
            }
            
            return false;
        });
        
        Gate::define('pay-commission', function ($user, $commission) {
            return in_array($user->role, ['admin', 'manager']) && 
                  ($user->role === 'admin' || $user->shops()->where('shops.id', $commission->shop_id)->exists());
        });
        
        Gate::define('view-vendor-report', function ($user, $vendor) {
            if ($user->role === 'admin') {
                return true;
            }
            
            if ($user->role === 'manager') {
                $managedShopIds = $user->shops()->wherePivot('is_manager', true)->pluck('shops.id')->toArray();
                $vendorShopIds = $vendor->shops()->pluck('shops.id')->toArray();
                return !empty(array_intersect($managedShopIds, $vendorShopIds));
            }
            
            return $user->id === $vendor->id;
        });
        
        // Gates pour les trocs
        Gate::define('manage-barters', function ($user) {
            return in_array($user->role, ['admin', 'manager', 'vendeur']);
        });
        
        // Gates pour les livraisons
        Gate::define('manage-deliveries', function ($user) {
            return in_array($user->role, ['admin', 'manager', 'vendeur']);
        });
    }
} 
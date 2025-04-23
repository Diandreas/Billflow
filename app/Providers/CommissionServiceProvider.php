<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Commission;
use App\Models\User;
use App\Models\Shop;

class CommissionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Autorisation pour voir les commissions
        Gate::define('view-commissions', function (User $user) {
            return $user->role === 'admin' || $user->role === 'manager';
        });

        // Autorisation pour voir une commission spécifique
        Gate::define('view-commission', function (User $user, Commission $commission) {
            // Admins et managers peuvent voir toutes les commissions
            if ($user->role === 'admin') {
                return true;
            }
            
            // Un manager peut voir les commissions des boutiques qu'il gère
            if ($user->role === 'manager') {
                $managedShopIds = $user->managedShops()->pluck('shops.id')->toArray();
                return in_array($commission->shop_id, $managedShopIds);
            }
            
            // Un vendeur ne peut voir que ses propres commissions
            if ($user->role === 'vendeur') {
                return $commission->user_id === $user->id;
            }
            
            return false;
        });

        // Autorisation pour voir le rapport d'un vendeur
        Gate::define('view-vendor-report', function (User $user, User $vendor) {
            // Seuls les admins et managers peuvent voir les rapports des vendeurs
            if (!in_array($user->role, ['admin', 'manager'])) {
                return false;
            }
            
            // Vérifier que c'est bien un vendeur
            if ($vendor->role !== 'vendeur') {
                return false;
            }
            
            // Un admin peut voir tous les vendeurs
            if ($user->role === 'admin') {
                return true;
            }
            
            // Un manager ne peut voir que les vendeurs des boutiques qu'il gère
            $managedShopIds = $user->managedShops()->pluck('shops.id')->toArray();
            $vendorShopIds = $vendor->shops()->pluck('shops.id')->toArray();
            return !empty(array_intersect($managedShopIds, $vendorShopIds));
        });

        // Autorisation pour payer une commission
        Gate::define('pay-commission', function (User $user, Commission $commission) {
            // Seuls les admins et managers peuvent payer des commissions
            if (!in_array($user->role, ['admin', 'manager'])) {
                return false;
            }
            
            // Un admin peut payer toutes les commissions
            if ($user->role === 'admin') {
                return true;
            }
            
            // Un manager ne peut payer que les commissions des boutiques qu'il gère
            $managedShopIds = $user->managedShops()->pluck('shops.id')->toArray();
            return in_array($commission->shop_id, $managedShopIds);
        });

        // Autorisation pour payer les commissions d'un vendeur
        Gate::define('pay-vendor-commissions', function (User $user, User $vendor) {
            // Seuls les admins et managers peuvent payer des commissions
            if (!in_array($user->role, ['admin', 'manager'])) {
                return false;
            }
            
            // Vérifier que c'est bien un vendeur
            if ($vendor->role !== 'vendeur') {
                return false;
            }
            
            // Un admin peut payer tous les vendeurs
            if ($user->role === 'admin') {
                return true;
            }
            
            // Un manager ne peut payer que les vendeurs des boutiques qu'il gère
            $managedShopIds = $user->managedShops()->pluck('shops.id')->toArray();
            $vendorShopIds = $vendor->shops()->pluck('shops.id')->toArray();
            return !empty(array_intersect($managedShopIds, $vendorShopIds));
        });
        
        // Autorisation pour gérer les paramètres des commissions
        Gate::define('manage-commissions-settings', function (User $user) {
            return $user->role === 'admin';
        });

        // Autorisation pour voir les commissions d'une boutique
        Gate::define('view-shop-commissions', function (User $user, Shop $shop) {
            // Seuls les admins et managers peuvent voir les rapports des boutiques
            if (!in_array($user->role, ['admin', 'manager'])) {
                return false;
            }
            
            // Un admin peut voir toutes les boutiques
            if ($user->role === 'admin') {
                return true;
            }
            
            // Un manager ne peut voir que les boutiques qu'il gère
            return $user->managedShops()->where('shops.id', $shop->id)->exists();
        });
    }
}

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
            return in_array($user->role, ['admin', 'manager']);
        });

        Gate::define('create-bill', function ($user) {
            return true; // Tous les utilisateurs authentifiés peuvent créer des factures
        });

        Gate::define('edit-bill', function ($user, $bill) {
            // Les admins peuvent modifier toutes les factures
            if ($user->role === 'admin') {
                return true;
            }

            // Les managers peuvent modifier les factures de leurs boutiques
            if ($user->role === 'manager' && $bill->shop_id) {
                return $user->shops()
                    ->where('shops.id', $bill->shop_id)
                    ->wherePivot('is_manager', true)
                    ->exists();
            }

            // Les utilisateurs peuvent modifier les factures qu'ils ont créées
            return $user->id === $bill->user_id || $user->id === $bill->seller_id;
        });

        Gate::define('delete-bill', function ($user, $bill) {
            // Seuls les admins et les managers peuvent supprimer des factures
            if ($user->role === 'admin') {
                return true;
            }

            // Les managers peuvent supprimer les factures de leurs boutiques
            if ($user->role === 'manager' && $bill->shop_id) {
                return $user->shops()
                    ->where('shops.id', $bill->shop_id)
                    ->wherePivot('is_manager', true)
                    ->exists();
            }

            return false;
        });
    }
} 
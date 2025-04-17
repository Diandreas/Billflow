<?php

namespace App\Policies;

use App\Models\Bill;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BillPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Tous les utilisateurs authentifiés peuvent voir la liste des factures
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Bill $bill): bool
    {
        // Les admins peuvent voir toutes les factures
        if ($user->role === 'admin') {
            return true;
        }

        // Les utilisateurs peuvent voir les factures des boutiques auxquelles ils sont assignés
        if ($bill->shop_id) {
            return $user->shops()->where('shops.id', $bill->shop_id)->exists();
        }

        // Les utilisateurs peuvent voir les factures qu'ils ont créées
        return $user->id === $bill->user_id || $user->id === $bill->seller_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true; // Tous les utilisateurs authentifiés peuvent créer des factures
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Bill $bill): bool
    {
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
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Bill $bill): bool
    {
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
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Bill $bill): bool
    {
        // Seuls les admins peuvent supprimer définitivement des factures
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can print the bill.
     */
    public function print(User $user, Bill $bill): bool
    {
        return $this->view($user, $bill);
    }

    /**
     * Determine whether the user can download the bill PDF.
     */
    public function downloadPdf(User $user, Bill $bill): bool
    {
        return $this->view($user, $bill);
    }

    /**
     * Determine whether the user can update the bill status.
     */
    public function updateStatus(User $user, Bill $bill): bool
    {
        return $this->update($user, $bill);
    }

    /**
     * Determine whether the user can add a signature to the bill.
     */
    public function addSignature(User $user, Bill $bill): bool
    {
        return $this->view($user, $bill);
    }
} 
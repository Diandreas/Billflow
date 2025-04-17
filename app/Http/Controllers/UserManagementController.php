<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class UserManagementController extends Controller
{
    /**
     * Affiche la liste des utilisateurs pour l'administration
     */
    public function index(Request $request)
    {
        // Vérifier que l'utilisateur est admin
        if (!auth()->user()->isAdmin()) {
            return redirect()->route('dashboard')
                ->with('error', 'Vous n\'avez pas les permissions nécessaires.');
        }

        $shopId = $request->input('shop_id');
        $role = $request->input('role');
        $search = $request->input('search');

        // Construire la requête
        $usersQuery = User::query();

        // Filtrer par boutique
        if ($shopId) {
            $usersQuery->whereHas('shops', function($query) use ($shopId) {
                $query->where('shops.id', $shopId);
            });
        }

        // Filtrer par rôle
        if ($role) {
            $usersQuery->where('role', $role);
        }

        // Recherche par nom ou email
        if ($search) {
            $usersQuery->where(function($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Récupérer les utilisateurs
        $users = $usersQuery->with('shops')->paginate(15)->withQueryString();

        // Liste des boutiques pour le filtre
        $shops = Shop::orderBy('name')->get();

        return view('users.index', compact('users', 'shops', 'shopId', 'role', 'search'));
    }

    /**
     * Affiche le formulaire pour réinitialiser l'email d'un utilisateur
     */
    public function showResetEmailForm(User $user)
    {
        // Vérifier que l'utilisateur est admin
        if (!auth()->user()->isAdmin()) {
            return redirect()->route('dashboard')
                ->with('error', 'Vous n\'avez pas les permissions nécessaires.');
        }

        // Récupérer les boutiques de l'utilisateur
        $userShops = $user->shops;

        return view('users.reset-email', compact('user', 'userShops'));
    }

    /**
     * Réinitialise l'email d'un utilisateur
     */
    public function resetEmail(Request $request, User $user)
    {
        // Vérifier que l'utilisateur est admin
        if (!auth()->user()->isAdmin()) {
            return redirect()->route('dashboard')
                ->with('error', 'Vous n\'avez pas les permissions nécessaires.');
        }

        // Valider les données
        $validated = $request->validate([
            'new_email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'confirm_password' => 'required',
        ]);

        // Vérifier le mot de passe de l'admin
        if (!Hash::check($validated['confirm_password'], auth()->user()->password)) {
            return back()->withErrors([
                'confirm_password' => 'Le mot de passe est incorrect.',
            ])->withInput();
        }

        // Enregistrer l'ancien email pour l'historique
        $oldEmail = $user->email;

        // Mettre à jour l'email
        $user->email = $validated['new_email'];
        $user->email_verified_at = null; // Réinitialiser la vérification de l'email
        $user->save();

        // Créer une entrée dans l'historique des actions
        // (Supposons qu'il existe une table pour l'historique des actions administratives)
        /*
        ActionLog::create([
            'user_id' => auth()->id(),
            'target_user_id' => $user->id,
            'action' => 'email_reset',
            'details' => json_encode([
                'old_email' => $oldEmail,
                'new_email' => $user->email,
            ]),
        ]);
        */

        return redirect()->route('users.index')
            ->with('success', 'L\'email de ' . $user->name . ' a été réinitialisé avec succès.');
    }

    /**
     * Affiche les détails d'un utilisateur
     */
    public function show(User $user)
    {
        // Vérifier que l'utilisateur est admin ou manager
        if (!auth()->user()->isAdmin() && !auth()->user()->isManager()) {
            return redirect()->route('dashboard')
                ->with('error', 'Vous n\'avez pas les permissions nécessaires.');
        }

        // Pour les managers, vérifier qu'ils ont accès à cet utilisateur
        if (auth()->user()->isManager() && !auth()->user()->isAdmin()) {
            $managedShopIds = auth()->user()->managedShops()->pluck('shops.id')->toArray();
            $userShopIds = $user->shops()->pluck('shops.id')->toArray();
            
            if (empty(array_intersect($managedShopIds, $userShopIds))) {
                return redirect()->route('dashboard')
                    ->with('error', 'Cet utilisateur n\'appartient pas à l\'une de vos boutiques.');
            }
        }

        // Récupérer les boutiques de l'utilisateur
        $userShops = $user->shops;

        return view('users.show', compact('user', 'userShops'));
    }
} 
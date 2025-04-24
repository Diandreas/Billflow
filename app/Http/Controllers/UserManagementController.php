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
        
        // Définir les rôles disponibles pour le filtre
        $roles = [
            (object)['name' => 'admin'],
            (object)['name' => 'manager'],
            (object)['name' => 'vendeur'],
            (object)['name' => 'utilisateur']
        ];

        return view('users.index', compact('users', 'shops', 'shopId', 'role', 'search', 'roles'));
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

    /**
     * Affiche le formulaire de création d'un utilisateur
     */
    public function create()
    {
        // Vérifier que l'utilisateur est admin
        if (!auth()->user()->isAdmin()) {
            return redirect()->route('dashboard')
                ->with('error', 'Vous n\'avez pas les permissions nécessaires.');
        }

        // Récupérer toutes les boutiques pour assignation
        $shops = Shop::orderBy('name')->get();
        
        return view('users.create', compact('shops'));
    }

    /**
     * Affiche le formulaire d'édition d'un utilisateur
     */
    public function edit(User $user)
    {
        // Vérifier que l'utilisateur est admin
        if (!auth()->user()->isAdmin()) {
            return redirect()->route('dashboard')
                ->with('error', 'Vous n\'avez pas les permissions nécessaires.');
        }
        
        // Récupérer toutes les boutiques pour assignation
        $shops = Shop::orderBy('name')->get();
        
        // Récupérer les boutiques de l'utilisateur
        $userShops = $user->shops;
        
        return view('users.edit', compact('user', 'shops', 'userShops'));
    }

    /**
     * Supprime un utilisateur du système
     */
    public function destroy(User $user)
    {
        // Vérifier que l'utilisateur est admin
        if (!auth()->user()->isAdmin()) {
            return redirect()->route('dashboard')
                ->with('error', 'Vous n\'avez pas les permissions nécessaires.');
        }

        // Empêcher la suppression de son propre compte via cette méthode
        if (auth()->id() === $user->id) {
            return redirect()->route('users.index')
                ->with('error', 'Vous ne pouvez pas supprimer votre propre compte via cette méthode.');
        }

        // Détacher l'utilisateur de toutes ses boutiques
        $user->shops()->detach();
        
        // Supprimer l'utilisateur
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'L\'utilisateur a été supprimé avec succès.');
    }

    /**
     * Enregistre un nouvel utilisateur
     */
    public function store(Request $request)
    {
        // Vérifier que l'utilisateur est admin
        if (!auth()->user()->isAdmin()) {
            return redirect()->route('dashboard')
                ->with('error', 'Vous n\'avez pas les permissions nécessaires.');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:255',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', 'string', Rule::in(['admin', 'manager', 'vendeur', 'utilisateur'])],
            'commission_rate' => 'nullable|required_if:role,vendeur|numeric|min:0|max:100',
            'is_active' => 'boolean',
            'shops' => 'nullable|array',
            'shops.*' => 'exists:shops,id',
            'manager_shops' => 'nullable|array',
            'manager_shops.*' => 'exists:shops,id',
        ]);

        // Créer l'utilisateur
        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'is_active' => $request->has('is_active'),
        ];
        
        // Ajouter le taux de commission pour les vendeurs
        if ($validated['role'] === 'vendeur' && isset($validated['commission_rate'])) {
            $userData['commission_rate'] = $validated['commission_rate'];
        }
        
        $user = User::create($userData);
        
        // Associer les boutiques à l'utilisateur si ce n'est pas un admin
        if ($validated['role'] !== 'admin' && !empty($validated['shops'])) {
            $shopData = [];
            
            foreach ($validated['shops'] as $shopId) {
                $isManager = !empty($validated['manager_shops']) && in_array($shopId, $validated['manager_shops']);
                
                $shopData[$shopId] = [
                    'is_manager' => $isManager,
                    'assigned_at' => now(),
                ];
            }
            
            if (!empty($shopData)) {
                $user->shops()->attach($shopData);
            }
        }
        
        return redirect()->route('users.index')
            ->with('success', 'L\'utilisateur a été créé avec succès.');
    }

    /**
     * Met à jour les informations d'un utilisateur
     */
    public function update(Request $request, User $user)
    {
        // Vérifier que l'utilisateur est admin
        if (!auth()->user()->isAdmin()) {
            return redirect()->route('dashboard')
                ->with('error', 'Vous n\'avez pas les permissions nécessaires.');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'phone' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
            'role' => ['required', 'string', Rule::in(['admin', 'manager', 'vendeur', 'utilisateur'])],
            'commission_rate' => 'nullable|required_if:role,vendeur|numeric|min:0|max:100',
            'is_active' => 'boolean',
            'shops' => 'nullable|array',
            'shops.*' => 'exists:shops,id',
            'manager_shops' => 'nullable|array',
            'manager_shops.*' => 'exists:shops,id',
        ]);
        
        // Mettre à jour les données de l'utilisateur
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'];
        $user->role = $validated['role'];
        $user->is_active = $request->has('is_active');
        
        // Mettre à jour le mot de passe si fourni
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
        
        // Mettre à jour le taux de commission pour les vendeurs
        if ($validated['role'] === 'vendeur' && isset($validated['commission_rate'])) {
            $user->commission_rate = $validated['commission_rate'];
        }
        
        $user->save();
        
        // Gérer les associations aux boutiques
        if ($validated['role'] !== 'admin') {
            $shopData = [];
            
            if (!empty($validated['shops'])) {
                foreach ($validated['shops'] as $shopId) {
                    $isManager = !empty($validated['manager_shops']) && in_array($shopId, $validated['manager_shops']);
                    
                    $shopData[$shopId] = [
                        'is_manager' => $isManager,
                        'assigned_at' => now(),
                    ];
                }
            }
            
            // Mettre à jour les associations
            $user->shops()->sync($shopData);
        } else {
            // Si l'utilisateur devient admin, supprimer toutes les associations
            $user->shops()->detach();
        }
        
        return redirect()->route('users.show', $user)
            ->with('success', 'L\'utilisateur a été mis à jour avec succès.');
    }
} 
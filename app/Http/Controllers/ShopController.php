<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ShopController extends Controller
{
    /**
     * Affiche la liste des boutiques
     */
    /**
     * Affiche la liste des boutiques avec filtrage et tri
     */
    public function index(Request $request)
    {
        $query = Shop::with('managers', 'users');
        $user = auth()->user();

        // Si l'utilisateur est un manager, filtrer pour ne montrer que ses boutiques
        if ($user->role === 'manager') {
            $query->whereHas('users', function($q) use ($user) {
                $q->where('users.id', $user->id)
                    ->where('shop_user.is_manager', true);
            });
        }

        // Recherche par texte
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filtrage par statut
        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Tri des résultats
        $sort = $request->input('sort', 'name');
        $direction = 'asc';

        if ($sort === 'created_at') {
            $query->orderBy('created_at', 'desc');
        } elseif ($sort === 'status') {
            $query->orderBy('is_active', 'desc');
        } else {
            $query->orderBy('name', 'asc');
        }

        // Récupération des statistiques - adaptées au contexte utilisateur
        if ($user->role === 'manager') {
            // Pour les managers, compter uniquement leurs boutiques
            $managerShopIds = $user->shops()
                ->where('shop_user.is_manager', true)
                ->pluck('shops.id');

            $totalShops = count($managerShopIds);
            $activeShops = Shop::whereIn('id', $managerShopIds)
                ->where('is_active', true)
                ->count();
            $inactiveShops = Shop::whereIn('id', $managerShopIds)
                ->where('is_active', false)
                ->count();
        } else {
            // Pour les admins, compter toutes les boutiques
            $totalShops = $query->count();
            $activeShops = Shop::where('is_active', true)->count();
            $inactiveShops = Shop::where('is_active', false)->count();
        }

        $shops = $query->paginate(15)->withQueryString();

        return view('shops.index', compact('shops', 'totalShops', 'activeShops', 'inactiveShops'));
    }
    /**
     * Affiche le formulaire de création d'une boutique
     */
    public function create()
    {
        return view('shops.create');
    }

    /**
     * Enregistre une nouvelle boutique
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('shop-logos', 'public');
            $validated['logo_path'] = $logoPath;
        }

        $shop = Shop::create($validated);

        // Enregistrer l'activité de création de boutique
        ActivityLogger::logCreated($shop, "Boutique {$shop->name} créée par " . Auth::user()?->name);

        return redirect()->route('shops.index')
            ->with('success', 'Boutique créée avec succès');
    }

    /**
     * Affiche les détails d'une boutique
     */
    public function show(Shop $shop)
    {
        $shop->load(['managers', 'vendors', 'bills']);

        // Préparer les statistiques des commissions pour chaque vendeur
        foreach ($shop->vendors as $vendor) {
            $vendorCommissions = \App\Models\Commission::where('user_id', $vendor->id)
                ->where('shop_id', $shop->id)
                ->get();

            $vendor->commission_stats = [
                'total' => $vendorCommissions->sum('amount'),
                'pending' => $vendorCommissions->where('is_paid', false)->sum('amount'),
                'paid' => $vendorCommissions->where('is_paid', true)->sum('amount'),
                'last_commission' => $vendorCommissions->sortByDesc('created_at')->first()
            ];
        }

        return view('shops.show', compact('shop'));
    }

    /**
     * Affiche le formulaire d'édition d'une boutique
     */
    public function edit(Shop $shop)
    {
        return view('shops.edit', compact('shop'));
    }

    /**
     * Met à jour les informations d'une boutique
     */
    public function update(Request $request, Shop $shop)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'sometimes|boolean',
        ]);

        // Sauvegarder les valeurs originales pour l'historique
        $oldValues = $shop->getOriginal();

        if ($request->hasFile('logo')) {
            // Supprimer l'ancien logo si existant
            if ($shop->logo_path) {
                Storage::disk('public')->delete($shop->logo_path);
            }

            $logoPath = $request->file('logo')->store('shop-logos', 'public');
            $validated['logo_path'] = $logoPath;
        }

        $shop->update($validated);

        // Enregistrer l'activité de mise à jour
        ActivityLogger::logUpdated($shop, $oldValues, "Boutique {$shop->name} modifiée par " . Auth::user()?->name);

        return redirect()->route('shops.show', $shop)
            ->with('success', 'Boutique mise à jour avec succès');
    }

    /**
     * Supprime une boutique
     */
    public function destroy(Shop $shop)
    {
        // Vérifier s'il y a des factures ou des trocs liés
        if ($shop->bills()->count() > 0 || $shop->barters()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Impossible de supprimer cette boutique car elle contient des factures ou des trocs');
        }

        // Enregistrer l'activité de suppression avant de supprimer la boutique
        ActivityLogger::logDeleted($shop, "Boutique {$shop->name} supprimée par " . Auth::user()?->name);

        // Supprimer le logo si existant
        if ($shop->logo_path) {
            Storage::disk('public')->delete($shop->logo_path);
        }

        $shop->delete();

        return redirect()->route('shops.index')
            ->with('success', 'Boutique supprimée avec succès');
    }

    /**
     * Gère l'assignation d'utilisateurs à une boutique
     */
    public function manageUsers(Shop $shop)
    {
        $users = User::where('role', '<>', 'admin')->get();
        $assignedUsers = $shop->users;

        return view('shops.manage-users', compact('shop', 'users', 'assignedUsers'));
    }

    /**
     * Ajoute ou met à jour des utilisateurs pour une boutique
     */
    public function assignUsers(Request $request, Shop $shop)
    {
        $validated = $request->validate([
            'users' => 'required|array',
            'users.*.id' => 'required|exists:users,id',
            'users.*.is_manager' => 'boolean',
            'users.*.custom_commission_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        foreach ($validated['users'] as $userData) {
            $shop->users()->syncWithoutDetaching([
                $userData['id'] => [
                    'is_manager' => $userData['is_manager'] ?? false,
                    'custom_commission_rate' => $userData['custom_commission_rate'],
                    'assigned_at' => now(),
                ]
            ]);
        }

        return redirect()->route('shops.show', $shop)
            ->with('success', 'Utilisateurs assignés avec succès');
    }

    /**
     * Retire un utilisateur d'une boutique
     */
    public function removeUser(Request $request, Shop $shop, User $user)
    {
        $shop->users()->detach($user->id);

        return redirect()->route('shops.show', $shop)
            ->with('success', 'Utilisateur retiré de la boutique avec succès');
    }
}

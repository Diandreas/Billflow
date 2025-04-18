<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ShopController extends Controller
{
    /**
     * Affiche la liste des boutiques
     */
    public function index()
    {
        $shops = Shop::with('managers')->get();
        return view('shops.index', compact('shops'));
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

        return redirect()->route('shops.index')
            ->with('success', 'Boutique créée avec succès');
    }

    /**
     * Affiche les détails d'une boutique
     */
    public function show(Shop $shop)
    {
        $shop->load(['managers', 'vendors']);
        
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

        if ($request->hasFile('logo')) {
            // Supprimer l'ancien logo si existant
            if ($shop->logo_path) {
                Storage::disk('public')->delete($shop->logo_path);
            }
            
            $logoPath = $request->file('logo')->store('shop-logos', 'public');
            $validated['logo_path'] = $logoPath;
        }

        $shop->update($validated);

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
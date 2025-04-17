<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Shop;
use App\Models\Commission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class VendorController extends Controller
{
    /**
     * Affiche la liste des vendeurs
     */
    public function index(Request $request)
    {
        // Vérifier si l'utilisateur est admin ou manager
        if (!auth()->user()->isAdmin() && !auth()->user()->isManager()) {
            return redirect()->route('dashboard')->with('error', 'Vous n\'avez pas les permissions nécessaires.');
        }
        
        $query = User::where('role', 'vendeur');
        
        // Si l'utilisateur est manager, ne montrer que les vendeurs de ses boutiques
        if (auth()->user()->isManager() && !auth()->user()->isAdmin()) {
            $shopIds = auth()->user()->managedShops()->pluck('shops.id')->toArray();
            $query->whereHas('shops', function($q) use ($shopIds) {
                $q->whereIn('shops.id', $shopIds);
            });
        }
        
        // Filtres
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        if ($request->has('shop_id')) {
            $shopId = $request->input('shop_id');
            $query->whereHas('shops', function($q) use ($shopId) {
                $q->where('shops.id', $shopId);
            });
        }
        
        // Tri
        $sort = $request->input('sort', 'name');
        $direction = $request->input('direction', 'asc');
        $query->orderBy($sort, $direction);
        
        $vendors = $query->paginate(15)->withQueryString();
        
        // Statistiques des vendeurs pour le mois en cours
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();
        
        $vendorIds = $vendors->pluck('id')->toArray();
        
        // Ventes du mois par vendeur
        $monthlySales = [];
        foreach ($vendorIds as $vendorId) {
            $monthlySales[$vendorId] = \App\Models\Bill::where('seller_id', $vendorId)
                ->whereBetween('date', [$monthStart, $monthEnd])
                ->where('status', '<>', 'cancelled')
                ->sum('total');
        }
        
        // Commissions du mois par vendeur
        $monthlyCommissions = [];
        foreach ($vendorIds as $vendorId) {
            $monthlyCommissions[$vendorId] = \App\Models\Commission::where('user_id', $vendorId)
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('amount');
        }
        
        // Boutiques (pour le filtre)
        if (auth()->user()->isAdmin()) {
            $shops = Shop::all();
        } else {
            $shops = auth()->user()->managedShops;
        }
        
        return view('vendors.index', compact(
            'vendors', 
            'shops', 
            'monthlySales', 
            'monthlyCommissions'
        ));
    }

    /**
     * Affiche le formulaire de création d'un vendeur
     */
    public function create()
    {
        // Vérifier si l'utilisateur est admin ou manager
        if (!auth()->user()->isAdmin() && !auth()->user()->isManager()) {
            return redirect()->route('dashboard')->with('error', 'Vous n\'avez pas les permissions nécessaires.');
        }
        
        // Récupérer les boutiques que l'utilisateur peut gérer
        if (auth()->user()->isAdmin()) {
            $shops = Shop::all();
        } else {
            $shops = auth()->user()->managedShops;
        }
        
        return view('vendors.create', compact('shops'));
    }

    /**
     * Enregistre un nouveau vendeur
     */
    public function store(Request $request)
    {
        // Vérifier si l'utilisateur est admin ou manager
        if (!auth()->user()->isAdmin() && !auth()->user()->isManager()) {
            return redirect()->route('dashboard')->with('error', 'Vous n\'avez pas les permissions nécessaires.');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'commission_rate' => 'required|numeric|min:0|max:100',
            'photo' => 'nullable|image|max:2048',
            'shops' => 'required|array',
            'shops.*' => 'exists:shops,id',
            'is_manager' => 'array',
            'is_manager.*' => 'boolean',
        ]);
        
        // Vérifier que l'utilisateur a accès aux boutiques sélectionnées
        if (!auth()->user()->isAdmin()) {
            $managedShopIds = auth()->user()->managedShops()->pluck('shops.id')->toArray();
            foreach ($validated['shops'] as $shopId) {
                if (!in_array($shopId, $managedShopIds)) {
                    return redirect()->back()->with('error', 'Vous n\'avez pas accès à toutes les boutiques sélectionnées.');
                }
            }
        }
        
        // Traiter la photo
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('vendors', 'public');
        }
        
        // Créer le vendeur
        $vendor = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'vendeur',
            'commission_rate' => $validated['commission_rate'],
            'photo_path' => $photoPath,
        ]);
        
        // Associer les boutiques au vendeur
        foreach ($validated['shops'] as $key => $shopId) {
            $isManager = isset($validated['is_manager'][$key]) && $validated['is_manager'][$key];
            
            // Vérifier que si c'est un manager, l'utilisateur qui le crée est admin
            if ($isManager && !auth()->user()->isAdmin()) {
                $isManager = false; // Seul un admin peut créer un manager
            }
            
            $vendor->shops()->attach($shopId, [
                'is_manager' => $isManager,
                'assigned_at' => now(),
            ]);
        }
        
        return redirect()->route('vendors.index')
            ->with('success', 'Vendeur créé avec succès');
    }

    /**
     * Affiche la page de détails d'un vendeur
     */
    public function show(User $vendor)
    {
        // Vérifier si l'utilisateur est admin ou manager
        if (!auth()->user()->isAdmin() && !auth()->user()->isManager()) {
            return redirect()->route('dashboard')->with('error', 'Vous n\'avez pas les permissions nécessaires.');
        }
        
        // Vérifier que c'est bien un vendeur
        if ($vendor->role !== 'vendeur') {
            return redirect()->route('vendors.index')->with('error', 'Utilisateur non trouvé.');
        }
        
        // Si l'utilisateur est manager, vérifier qu'il a accès à ce vendeur
        if (auth()->user()->isManager() && !auth()->user()->isAdmin()) {
            $managedShopIds = auth()->user()->managedShops()->pluck('shops.id')->toArray();
            $vendorShopIds = $vendor->shops()->pluck('shops.id')->toArray();
            
            // Vérifier qu'il y a au moins une boutique en commun
            if (empty(array_intersect($managedShopIds, $vendorShopIds))) {
                return redirect()->route('vendors.index')->with('error', 'Vous n\'avez pas accès à ce vendeur.');
            }
        }
        
        // Statistiques de vente pour les 30 derniers jours
        $thirtyDaysAgo = now()->subDays(30);
        
        $stats = [
            'sales_count' => $vendor->sales()->where('date', '>=', $thirtyDaysAgo)->count(),
            'sales_total' => $vendor->sales()->where('date', '>=', $thirtyDaysAgo)->sum('total'),
            'barters_count' => $vendor->barters()->where('date', '>=', $thirtyDaysAgo)->count(),
            'commission_total' => $vendor->commissions()->where('created_at', '>=', $thirtyDaysAgo)->sum('amount'),
            'unpaid_commission' => $vendor->commissions()->where('is_paid', false)->sum('amount'),
        ];
        
        // Ventes par boutique
        $shopSales = [];
        foreach ($vendor->shops as $shop) {
            $shopSales[$shop->id] = [
                'name' => $shop->name,
                'total' => $vendor->sales()->where('shop_id', $shop->id)->where('date', '>=', $thirtyDaysAgo)->sum('total'),
                'count' => $vendor->sales()->where('shop_id', $shop->id)->where('date', '>=', $thirtyDaysAgo)->count(),
            ];
        }
        
        return view('vendors.show', compact('vendor', 'stats', 'shopSales'));
    }

    /**
     * Affiche le formulaire de modification d'un vendeur
     */
    public function edit(User $vendor)
    {
        // Vérifier si l'utilisateur est admin ou manager
        if (!auth()->user()->isAdmin() && !auth()->user()->isManager()) {
            return redirect()->route('dashboard')->with('error', 'Vous n\'avez pas les permissions nécessaires.');
        }
        
        // Vérifier que c'est bien un vendeur
        if ($vendor->role !== 'vendeur') {
            return redirect()->route('vendors.index')->with('error', 'Utilisateur non trouvé.');
        }
        
        // Si l'utilisateur est manager, vérifier qu'il a accès à ce vendeur
        if (auth()->user()->isManager() && !auth()->user()->isAdmin()) {
            $managedShopIds = auth()->user()->managedShops()->pluck('shops.id')->toArray();
            $vendorShopIds = $vendor->shops()->pluck('shops.id')->toArray();
            
            // Vérifier qu'il y a au moins une boutique en commun
            if (empty(array_intersect($managedShopIds, $vendorShopIds))) {
                return redirect()->route('vendors.index')->with('error', 'Vous n\'avez pas accès à ce vendeur.');
            }
        }
        
        // Récupérer les boutiques que l'utilisateur peut gérer
        if (auth()->user()->isAdmin()) {
            $shops = Shop::all();
        } else {
            $shops = auth()->user()->managedShops;
        }
        
        // Récupérer les boutiques du vendeur avec des infos supplémentaires
        $vendorShops = $vendor->shops()->get()->map(function ($shop) {
            return [
                'id' => $shop->id,
                'is_manager' => $shop->pivot->is_manager,
            ];
        })->keyBy('id');
        
        return view('vendors.edit', compact('vendor', 'shops', 'vendorShops'));
    }

    /**
     * Met à jour un vendeur
     */
    public function update(Request $request, User $vendor)
    {
        // Vérifier si l'utilisateur est admin ou manager
        if (!auth()->user()->isAdmin() && !auth()->user()->isManager()) {
            return redirect()->route('dashboard')->with('error', 'Vous n\'avez pas les permissions nécessaires.');
        }
        
        // Vérifier que c'est bien un vendeur
        if ($vendor->role !== 'vendeur') {
            return redirect()->route('vendors.index')->with('error', 'Utilisateur non trouvé.');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($vendor->id),
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'commission_rate' => 'required|numeric|min:0|max:100',
            'photo' => 'nullable|image|max:2048',
            'shops' => 'required|array',
            'shops.*' => 'exists:shops,id',
            'is_manager' => 'array',
            'is_manager.*' => 'boolean',
        ]);
        
        // Vérifier que l'utilisateur a accès aux boutiques sélectionnées
        if (!auth()->user()->isAdmin()) {
            $managedShopIds = auth()->user()->managedShops()->pluck('shops.id')->toArray();
            foreach ($validated['shops'] as $shopId) {
                if (!in_array($shopId, $managedShopIds)) {
                    return redirect()->back()->with('error', 'Vous n\'avez pas accès à toutes les boutiques sélectionnées.');
                }
            }
        }
        
        // Traiter la photo
        if ($request->hasFile('photo')) {
            // Supprimer l'ancienne photo
            if ($vendor->photo_path) {
                Storage::disk('public')->delete($vendor->photo_path);
            }
            
            $photoPath = $request->file('photo')->store('vendors', 'public');
            $vendor->photo_path = $photoPath;
        }
        
        // Mettre à jour le vendeur
        $vendor->name = $validated['name'];
        $vendor->email = $validated['email'];
        $vendor->commission_rate = $validated['commission_rate'];
        
        if (!empty($validated['password'])) {
            $vendor->password = Hash::make($validated['password']);
        }
        
        $vendor->save();
        
        // Mettre à jour les boutiques du vendeur
        $syncData = [];
        foreach ($validated['shops'] as $key => $shopId) {
            $isManager = isset($validated['is_manager'][$key]) && $validated['is_manager'][$key];
            
            // Vérifier que si c'est un manager, l'utilisateur qui le modifie est admin
            if ($isManager && !auth()->user()->isAdmin()) {
                // Vérifier si le vendeur était déjà manager de cette boutique
                $existingPivot = $vendor->shops()->where('shops.id', $shopId)->first()?->pivot;
                $isManager = $existingPivot && $existingPivot->is_manager;
            }
            
            $syncData[$shopId] = [
                'is_manager' => $isManager,
                'assigned_at' => now(),
            ];
        }
        
        $vendor->shops()->sync($syncData);
        
        return redirect()->route('vendors.index')
            ->with('success', 'Vendeur mis à jour avec succès');
    }

    /**
     * Supprime un vendeur
     */
    public function destroy(User $vendor)
    {
        // Vérifier si l'utilisateur est admin
        if (!auth()->user()->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Vous n\'avez pas les permissions nécessaires.');
        }
        
        // Vérifier que c'est bien un vendeur
        if ($vendor->role !== 'vendeur') {
            return redirect()->route('vendors.index')->with('error', 'Utilisateur non trouvé.');
        }
        
        // Vérifier si le vendeur a des ventes ou des commissions
        $hasSales = $vendor->sales()->count() > 0;
        $hasCommissions = $vendor->commissions()->count() > 0;
        
        if ($hasSales || $hasCommissions) {
            return redirect()->route('vendors.index')
                ->with('error', 'Impossible de supprimer ce vendeur car il a des ventes ou des commissions associées.');
        }
        
        // Supprimer la photo
        if ($vendor->photo_path) {
            Storage::disk('public')->delete($vendor->photo_path);
        }
        
        // Détacher les boutiques
        $vendor->shops()->detach();
        
        // Supprimer le vendeur
        $vendor->delete();
        
        return redirect()->route('vendors.index')
            ->with('success', 'Vendeur supprimé avec succès');
    }

    /**
     * Affiche les commissions d'un vendeur
     */
    public function commissions(User $vendor)
    {
        // Vérifier si l'utilisateur est admin ou manager
        if (!auth()->user()->isAdmin() && !auth()->user()->isManager()) {
            return redirect()->route('dashboard')->with('error', 'Vous n\'avez pas les permissions nécessaires.');
        }
        
        // Vérifier que c'est bien un vendeur
        if ($vendor->role !== 'vendeur') {
            return redirect()->route('vendors.index')->with('error', 'Utilisateur non trouvé.');
        }
        
        // Si l'utilisateur est manager, vérifier qu'il a accès à ce vendeur
        if (auth()->user()->isManager() && !auth()->user()->isAdmin()) {
            $managedShopIds = auth()->user()->managedShops()->pluck('shops.id')->toArray();
            $vendorShopIds = $vendor->shops()->pluck('shops.id')->toArray();
            
            // Vérifier qu'il y a au moins une boutique en commun
            if (empty(array_intersect($managedShopIds, $vendorShopIds))) {
                return redirect()->route('vendors.index')->with('error', 'Vous n\'avez pas accès à ce vendeur.');
            }
            
            // Limiter les commissions aux boutiques gérées
            $commissions = $vendor->commissions()
                ->whereIn('shop_id', $managedShopIds)
                ->with(['bill', 'barter', 'shop'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);
                
            $totalCommissions = $vendor->commissions()->whereIn('shop_id', $managedShopIds)->sum('amount');
            $paidCommissions = $vendor->commissions()->whereIn('shop_id', $managedShopIds)->where('is_paid', true)->sum('amount');
        } else {
            // Récupérer toutes les commissions
            $commissions = $vendor->commissions()
                ->with(['bill', 'barter', 'shop'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);
                
            $totalCommissions = $vendor->commissions()->sum('amount');
            $paidCommissions = $vendor->commissions()->where('is_paid', true)->sum('amount');
        }
        
        $pendingCommissions = $totalCommissions - $paidCommissions;
        
        // Statistiques par type de commission
        $statsByType = [
            'vente' => [
                'count' => $vendor->commissions()->where('type', 'vente')->count(),
                'amount' => $vendor->commissions()->where('type', 'vente')->sum('amount'),
            ],
            'troc' => [
                'count' => $vendor->commissions()->where('type', 'troc')->count(),
                'amount' => $vendor->commissions()->where('type', 'troc')->sum('amount'),
            ],
            'surplus' => [
                'count' => $vendor->commissions()->where('type', 'surplus')->count(),
                'amount' => $vendor->commissions()->where('type', 'surplus')->sum('amount'),
            ],
        ];
        
        return view('vendors.commissions', compact(
            'vendor', 
            'commissions', 
            'totalCommissions', 
            'paidCommissions', 
            'pendingCommissions',
            'statsByType'
        ));
    }

    /**
     * Marque des commissions comme payées
     */
    public function payCommissions(Request $request, User $vendor)
    {
        // Vérifier si l'utilisateur est admin ou manager
        if (!auth()->user()->isAdmin() && !auth()->user()->isManager()) {
            return redirect()->route('dashboard')->with('error', 'Vous n\'avez pas les permissions nécessaires.');
        }
        
        // Vérifier que c'est bien un vendeur
        if ($vendor->role !== 'vendeur') {
            return redirect()->route('vendors.index')->with('error', 'Utilisateur non trouvé.');
        }
        
        $validated = $request->validate([
            'commission_ids' => 'required|array',
            'commission_ids.*' => 'exists:commissions,id',
            'payment_note' => 'nullable|string|max:255',
        ]);
        
        // Récupérer les commissions à payer
        $commissions = Commission::whereIn('id', $validated['commission_ids'])
            ->where('user_id', $vendor->id)
            ->where('is_paid', false);
            
        // Si l'utilisateur est manager, limiter aux boutiques gérées
        if (auth()->user()->isManager() && !auth()->user()->isAdmin()) {
            $managedShopIds = auth()->user()->managedShops()->pluck('shops.id')->toArray();
            $commissions->whereIn('shop_id', $managedShopIds);
        }
        
        $commissions = $commissions->get();
        
        // Marquer les commissions comme payées
        foreach ($commissions as $commission) {
            $commission->is_paid = true;
            $commission->paid_at = now();
            $commission->paid_by = auth()->id();
            
            if (!empty($validated['payment_note'])) {
                $commission->description = ($commission->description ? $commission->description . "\n" : '') 
                    . "Paiement: " . $validated['payment_note'];
            }
            
            $commission->save();
        }
        
        $totalPaid = $commissions->sum('amount');
        
        return redirect()->route('vendors.commissions', $vendor)
            ->with('success', 'Commissions marquées comme payées (' . number_format($totalPaid, 2) . ' XAF)');
    }
} 
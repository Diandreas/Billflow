<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Shop;
use App\Models\Commission;
use App\Models\Bill;
use App\Models\CommissionPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Spatie\Activitylog\Facades\LogActivity;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CommissionsExport;
use Spatie\Activitylog\Models\Activity;

class CommissionController extends Controller
{
    /**
     * Affiche la liste des commissions
     */
    public function index(Request $request)
    {
        // Vérifier les autorisations
        if (!Gate::allows('view-commissions')) {
            abort(403, 'Action non autorisée.');
        }

        $query = Commission::with(['user', 'bill', 'shop']);

        // Filtrer par statut
        if ($request->has('status')) {
            if ($request->input('status') === 'pending') {
                $query->where('is_paid', false);
            } elseif ($request->input('status') === 'paid') {
                $query->where('is_paid', true);
            }
        }

        // Filtrer par vendeur
        if ($request->has('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        // Filtrer par boutique
        if ($request->has('shop_id')) {
            $query->where('shop_id', $request->input('shop_id'));
        }

        // Filtrer par période
        if ($request->has('period_start')) {
            $query->where('created_at', '>=', $request->input('period_start'));
        }
        if ($request->has('period_end')) {
            $query->where('created_at', '<=', $request->input('period_end'));
        }

        // Obtenir les commissions
        $commissions = $query->orderBy('created_at', 'desc')->paginate(15);

        // Obtenir les vendeurs et boutiques pour les filtres
        $sellers = User::where('role', 'vendeur')->orderBy('name')->get();
        $shops = Gate::allows('admin') 
            ? Shop::orderBy('name')->get() 
            : Auth::user()->shops;
            
        // Liste des mois pour le filtre
        $months = [
            '01' => 'Janvier',
            '02' => 'Février',
            '03' => 'Mars',
            '04' => 'Avril',
            '05' => 'Mai',
            '06' => 'Juin',
            '07' => 'Juillet',
            '08' => 'Août',
            '09' => 'Septembre',
            '10' => 'Octobre',
            '11' => 'Novembre',
            '12' => 'Décembre',
        ];

        // Statistiques
        $stats = [
            'total_commissions' => Commission::sum('amount'),
            'pending_commissions' => Commission::where('is_paid', false)->sum('amount'),
            'paid_commissions' => Commission::where('is_paid', true)->sum('amount'),
        ];

        return view('commissions.index', compact('commissions', 'sellers', 'shops', 'stats', 'months'));
    }

    /**
     * Affiche le rapport de commissions d'un vendeur
     */
    public function vendorReport(User $user)
    {
        // Vérifier les autorisations
        if (!Gate::allows('view-vendor-report', $user)) {
            abort(403, 'Action non autorisée.');
        }

        $user->load('shops');

        // Obtenir les commissions du vendeur
        $commissions = Commission::where('user_id', $user->id)
            ->with(['bill', 'shop'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Statistiques
        $stats = [
            'total_commissions' => Commission::where('user_id', $user->id)->sum('amount'),
            'pending_commissions' => Commission::where('user_id', $user->id)->where('is_paid', false)->sum('amount'),
            'paid_commissions' => Commission::where('user_id', $user->id)->where('is_paid', true)->sum('amount'),
            'total_sales' => Bill::where('seller_id', $user->id)->sum('total'),
            'total_bills' => Bill::where('seller_id', $user->id)->count(),
        ];

        // Obtenir les commissions par mois
        $monthlySales = Bill::where('seller_id', $user->id)
            ->selectRaw('YEAR(date) as year, MONTH(date) as month, SUM(total) as total, COUNT(*) as count')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        $monthlyCommissions = Commission::where('user_id', $user->id)
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        return view('commissions.vendor-report', compact('user', 'commissions', 'stats', 'monthlySales', 'monthlyCommissions'));
    }

    /**
     * Exporte les commissions au format CSV
     */
    public function export(Request $request)
    {
        // Vérifier l'autorisation
        if (!(Auth::user()->role === 'admin' || Auth::user()->role === 'manager')) {
            return redirect()->route('dashboard')
                ->with('error', 'Vous n\'avez pas les permissions nécessaires.');
        }

        // Paramètres de filtre
        $vendorId = $request->input('vendor_id');
        $shopId = $request->input('shop_id');
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        $status = $request->input('status');

        // Construire la requête
        $commissionsQuery = Commission::with(['user', 'bill'])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        // Appliquer les filtres
        if ($vendorId) {
            $commissionsQuery->where('user_id', $vendorId);
        }

        if ($shopId) {
            $commissionsQuery->whereHas('bill', function($query) use ($shopId) {
                $query->where('shop_id', $shopId);
            });
        }

        if ($status === 'pending') {
            $commissionsQuery->where('is_paid', false);
        } elseif ($status === 'paid') {
            $commissionsQuery->where('is_paid', true);
        }

        // Restreindre pour les managers
        if (Auth::user()->role === 'manager' && Auth::user()->role !== 'admin') {
            $managedShopIds = Auth::user()->shops->pluck('id')->toArray();
            $commissionsQuery->whereHas('bill', function($query) use ($managedShopIds) {
                $query->whereIn('shop_id', $managedShopIds);
            });
        }

        // Récupérer toutes les commissions
        $commissions = $commissionsQuery->orderBy('created_at', 'desc')->get();

        // Préparer les en-têtes CSV
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="commissions.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        // Créer le CSV
        $callback = function() use ($commissions) {
            $file = fopen('php://output', 'w');
            
            // En-têtes des colonnes
            fputcsv($file, [
                'ID',
                'Vendeur',
                'Facture',
                'Montant',
                'Source',
                'Statut',
                'Date',
                'Boutique'
            ]);
            
            // Données
            foreach ($commissions as $commission) {
                $shopName = $commission->bill && $commission->bill->shop 
                    ? $commission->bill->shop->name 
                    : 'N/A';
                    
                fputcsv($file, [
                    $commission->id,
                    $commission->user->name,
                    $commission->bill ? $commission->bill->reference : 'N/A',
                    $commission->amount,
                    $commission->source,
                    $commission->is_paid ? 'Payée' : 'En attente',
                    $commission->created_at->format('d/m/Y H:i'),
                    $shopName
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Affiche les détails d'une commission
     */
    public function show(Commission $commission)
    {
        // Vérifier les autorisations
        if (!Gate::allows('view-commission', $commission)) {
            abort(403, 'Action non autorisée.');
        }

        $commission->load(['user', 'bill.client', 'shop']);

        return view('commissions.show', compact('commission'));
    }

    /**
     * Marque une commission comme payée
     */
    public function markAsPaid(Request $request, Commission $commission)
    {
        // Vérifier les autorisations
        if (!Gate::allows('pay-commission', $commission)) {
            abort(403, 'Action non autorisée.');
        }

        $validated = $request->validate([
            'payment_method' => 'required|string',
            'payment_reference' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        // Créer un nouveau paiement pour cette commission
        $payment = CommissionPayment::create([
            'reference' => CommissionPayment::generateReference(),
            'shop_id' => $commission->shop_id,
            'user_id' => $commission->user_id,
            'paid_by' => Auth::id(),
            'amount' => $commission->amount,
            'payment_method' => $validated['payment_method'],
            'payment_reference' => $validated['payment_reference'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'paid_at' => now(),
        ]);

        // Mettre à jour la commission
        $commission->update([
            'is_paid' => true,
            'paid_at' => now(),
            'paid_by' => Auth::id(),
            'payment_method' => $validated['payment_method'],
            'payment_reference' => $validated['payment_reference'] ?? null,
            'payment_group_id' => $payment->id,
            'payment_notes' => $validated['notes'] ?? null,
        ]);

        // Enregistrer l'activité
        // TODO: Décommenter et implémenter correctement l'activité log
        /*
        activity()
            ->performedOn($commission)
            ->causedBy(Auth::user())
            ->log('Commission marquée comme payée');
        */

        return redirect()->route('commissions.show', $commission)
            ->with('success', 'Commission marquée comme payée avec succès.');
    }

    /**
     * Payer un lot de commissions pour un vendeur spécifique
     */
    public function payBatch(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'shop_id' => 'required|exists:shops,id',
            'commission_ids' => 'required|array',
            'commission_ids.*' => 'exists:commissions,id',
            'payment_method' => 'required|string',
            'payment_reference' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        // Vérifier les autorisations
        $vendor = User::findOrFail($validated['user_id']);
        if (!Gate::allows('pay-vendor-commissions', $vendor)) {
            abort(403, 'Action non autorisée.');
        }

        // Récupérer les commissions concernées
        $commissions = Commission::whereIn('id', $validated['commission_ids'])
            ->where('user_id', $validated['user_id'])
            ->where('shop_id', $validated['shop_id'])
            ->where('is_paid', false)
            ->get();

        if ($commissions->isEmpty()) {
            return back()->with('error', 'Aucune commission valide trouvée pour ce paiement.');
        }

        // Calculer le montant total
        $totalAmount = $commissions->sum('amount');

        // Créer un paiement groupé
        $payment = CommissionPayment::create([
            'reference' => CommissionPayment::generateReference(),
            'shop_id' => $validated['shop_id'],
            'user_id' => $validated['user_id'],
            'paid_by' => Auth::id(),
            'amount' => $totalAmount,
            'payment_method' => $validated['payment_method'],
            'payment_reference' => $validated['payment_reference'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'paid_at' => now(),
        ]);

        // Mettre à jour chaque commission
        foreach ($commissions as $commission) {
            $commission->update([
                'is_paid' => true,
                'paid_at' => now(),
                'paid_by' => Auth::id(),
                'payment_method' => $validated['payment_method'],
                'payment_reference' => $validated['payment_reference'] ?? null,
                'payment_group_id' => $payment->id,
                'payment_notes' => $validated['notes'] ?? null,
            ]);
        }

        return redirect()->route('commissions.vendor-report', $vendor)
            ->with('success', 'Toutes les commissions sélectionnées ont été marquées comme payées.');
    }

    /**
     * Affiche le formulaire de création d'une commission
     */
    public function create()
    {
        // Vérifier les autorisations
        if (!Gate::allows('view-commissions')) {
            abort(403, 'Action non autorisée.');
        }

        $sellers = User::where('role', 'vendeur')->orderBy('name')->get();
        $shops = Gate::allows('admin') 
            ? Shop::orderBy('name')->get() 
            : Auth::user()->shops;

        return view('commissions.create', compact('sellers', 'shops'));
    }

    /**
     * Enregistre une nouvelle commission
     */
    public function store(Request $request)
    {
        // Vérifier les autorisations
        if (!Gate::allows('view-commissions')) {
            abort(403, 'Action non autorisée.');
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'shop_id' => 'required|exists:shops,id',
            'amount' => 'required|numeric|min:0',
            'period_month' => 'required|string',
            'period_year' => 'required|integer',
            'notes' => 'nullable|string',
        ]);

        // Générer une référence unique
        $reference = 'COM-' . date('Ymd') . '-' . rand(1000, 9999);

        $commission = Commission::create([
            'user_id' => $validated['user_id'],
            'shop_id' => $validated['shop_id'],
            'amount' => $validated['amount'],
            'reference' => $reference,
            'period_month' => $validated['period_month'],
            'period_year' => $validated['period_year'],
            'notes' => $validated['notes'] ?? null,
            'status' => 'pending',
            'is_paid' => false,
        ]);

        // Enregistrer l'activité
        // TODO: Décommenter et implémenter correctement l'activité log
        /*
        activity()
            ->performedOn($commission)
            ->causedBy(Auth::user())
            ->log('Commission créée');
        */

        return redirect()->route('commissions.index')
            ->with('status', 'Commission créée avec succès.');
    }

    /**
     * Affiche le formulaire d'édition d'une commission
     */
    public function edit(Commission $commission)
    {
        // Vérifier les autorisations
        if (!Gate::allows('view-commission', $commission)) {
            abort(403, 'Action non autorisée.');
        }

        $sellers = User::where('role', 'vendeur')->orderBy('name')->get();
        $shops = Gate::allows('admin') 
            ? Shop::orderBy('name')->get() 
            : Auth::user()->shops;

        return view('commissions.edit', compact('commission', 'sellers', 'shops'));
    }

    /**
     * Met à jour une commission
     */
    public function update(Request $request, Commission $commission)
    {
        // Vérifier les autorisations
        if (!Gate::allows('view-commission', $commission)) {
            abort(403, 'Action non autorisée.');
        }

        // Seules les commissions non payées peuvent être modifiées
        if ($commission->is_paid) {
            return back()->with('error', 'Les commissions déjà payées ne peuvent pas être modifiées.');
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'shop_id' => 'required|exists:shops,id',
            'amount' => 'required|numeric|min:0',
            'period_month' => 'required|string',
            'period_year' => 'required|integer',
            'notes' => 'nullable|string',
        ]);

        $commission->update([
            'user_id' => $validated['user_id'],
            'shop_id' => $validated['shop_id'],
            'amount' => $validated['amount'],
            'period_month' => $validated['period_month'],
            'period_year' => $validated['period_year'],
            'notes' => $validated['notes'] ?? null,
        ]);

        // Enregistrer l'activité
        // TODO: Décommenter et implémenter correctement l'activité log
        /*
        activity()
            ->performedOn($commission)
            ->causedBy(Auth::user())
            ->log('Commission modifiée');
        */

        return redirect()->route('commissions.show', $commission)
            ->with('status', 'Commission mise à jour avec succès.');
    }

    /**
     * Supprime une commission
     */
    public function destroy(Commission $commission)
    {
        // Vérifier les autorisations
        if (!Gate::allows('view-commission', $commission)) {
            abort(403, 'Action non autorisée.');
        }

        // Seules les commissions non payées peuvent être supprimées
        if ($commission->is_paid) {
            return back()->with('error', 'Les commissions déjà payées ne peuvent pas être supprimées.');
        }

        // Enregistrer l'activité avant la suppression
        // TODO: Décommenter et implémenter correctement l'activité log
        /*
        activity()
            ->performedOn($commission)
            ->causedBy(Auth::user())
            ->log('Commission supprimée');
        */

        $commission->delete();

        return redirect()->route('commissions.index')
            ->with('status', 'Commission supprimée avec succès.');
    }

    /**
     * Affiche les commissions pour une boutique spécifique avec statistiques par vendeur
     *
     * @param  int  $shopId
     * @return \Illuminate\Http\Response
     */
    public function shopReport($shopId)
    {
        // Vérifier les autorisations
        if (!auth()->user()->can('view shop commissions')) {
            abort(403, 'Accès non autorisé');
        }
        
        // Récupérer la boutique
        $shop = Shop::findOrFail($shopId);
        
        // Récupérer tous les vendeurs associés à cette boutique
        $vendors = User::role('vendeur')
                    ->whereHas('commissions.bill', function($query) use ($shopId) {
                        $query->where('shop_id', $shopId);
                    })
                    ->get();
        
        // Calculer les statistiques pour chaque vendeur
        $vendorStats = [];
        $totalStats = [
            'total_amount' => 0,
            'pending_amount' => 0,
            'paid_amount' => 0
        ];
        
        foreach ($vendors as $vendor) {
            $stats = $this->getVendorStats($vendor, $shopId);
            $vendorStats[$vendor->id] = $stats;
            
            // Ajouter aux totaux
            $totalStats['total_amount'] += $stats['total_amount'];
            $totalStats['pending_amount'] += $stats['pending_amount'];
            $totalStats['paid_amount'] += $stats['paid_amount'];
        }
        
        // Récupérer les commissions en attente pour cette boutique
        $pendingCommissions = Commission::whereHas('bill', function($query) use ($shopId) {
                                $query->where('shop_id', $shopId);
                            })
                            ->whereNull('paid_at')
                            ->with(['user', 'bill'])
                            ->orderBy('created_at', 'desc')
                            ->paginate(10);
        
        // Récupérer les paiements récents
        $recentPayments = CommissionPayment::whereHas('commissions.bill', function($query) use ($shopId) {
                                $query->where('shop_id', $shopId);
                            })
                            ->with(['user'])
                            ->latest('paid_at')
                            ->take(5)
                            ->get();
        
        return view('commissions.shop-report', [
            'shop' => $shop,
            'vendors' => $vendors,
            'vendorStats' => $vendorStats,
            'totalStats' => $totalStats,
            'pendingCommissions' => $pendingCommissions,
            'recentPayments' => $recentPayments
        ]);
    }

    /**
     * Calcule les statistiques de commissions pour un vendeur dans une boutique spécifique
     *
     * @param  \App\Models\User  $vendor
     * @param  int  $shopId
     * @return array
     */
    private function getVendorStats(User $vendor, $shopId)
    {
        // Calculer le montant total des commissions
        $totalAmount = Commission::where('user_id', $vendor->id)
                        ->whereHas('bill', function($query) use ($shopId) {
                            $query->where('shop_id', $shopId);
                        })
                        ->sum('amount');
        
        // Calculer le montant des commissions payées
        $paidAmount = Commission::where('user_id', $vendor->id)
                        ->whereHas('bill', function($query) use ($shopId) {
                            $query->where('shop_id', $shopId);
                        })
                        ->whereNotNull('paid_at')
                        ->sum('amount');
        
        // Calculer le montant des commissions en attente
        $pendingAmount = Commission::where('user_id', $vendor->id)
                            ->whereHas('bill', function($query) use ($shopId) {
                                $query->where('shop_id', $shopId);
                            })
                            ->whereNull('paid_at')
                            ->sum('amount');
        
        // Récupérer la date du dernier paiement
        $lastPayment = CommissionPayment::whereHas('commissions', function($query) use ($vendor, $shopId) {
                            $query->where('user_id', $vendor->id)
                                ->whereHas('bill', function($q) use ($shopId) {
                                    $q->where('shop_id', $shopId);
                                });
                        })
                        ->latest('paid_at')
                        ->first();
        
        return [
            'total_amount' => $totalAmount,
            'paid_amount' => $paidAmount,
            'pending_amount' => $pendingAmount,
            'last_payment' => $lastPayment,
        ];
    }

    /**
     * Marquer une commission individuelle comme payée
     *
     * @param  int  $commissionId
     * @return \Illuminate\Http\Response
     */
    public function payCommission($commissionId)
    {
        // Vérifier les autorisations
        if (!auth()->user()->can('pay commissions')) {
            abort(403, 'Accès non autorisé');
        }
        
        $commission = Commission::findOrFail($commissionId);
        
        // Vérifier que la commission n'est pas déjà payée
        if (!is_null($commission->paid_at)) {
            return redirect()->back()->with('error', 'Cette commission a déjà été payée.');
        }
        
        DB::beginTransaction();
        
        try {
            // Créer un nouveau paiement
            $payment = new CommissionPayment();
            $payment->user_id = $commission->user_id;
            $payment->paid_by = auth()->id();
            $payment->amount = $commission->amount;
            $payment->paid_at = now();
            $payment->payment_note = "Paiement individuel";
            $payment->save();
            
            // Mettre à jour la commission
            $commission->paid_at = now();
            $commission->commission_payment_id = $payment->id;
            $commission->save();
            
            DB::commit();
            
            return redirect()->back()->with('success', 'Commission payée avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erreur lors du paiement de la commission: ' . $e->getMessage());
        }
    }

    /**
     * Payer toutes les commissions en attente pour un vendeur spécifique
     *
     * @param  int  $userId
     * @return \Illuminate\Http\Response
     */
    public function payVendorCommissions($userId)
    {
        // Vérifier les autorisations
        if (!auth()->user()->can('pay commissions')) {
            abort(403, 'Accès non autorisé');
        }
        
        // Récupérer toutes les commissions impayées du vendeur
        $pendingCommissions = Commission::where('user_id', $userId)
            ->whereNull('paid_at')
            ->get();
        
        if ($pendingCommissions->isEmpty()) {
            return redirect()->back()->with('info', 'Aucune commission en attente pour ce vendeur.');
        }
        
        $totalAmount = $pendingCommissions->sum('amount');
        
        DB::beginTransaction();
        
        try {
            // Créer un nouveau paiement groupé
            $payment = new CommissionPayment();
            $payment->user_id = $userId;
            $payment->paid_by = auth()->id();
            $payment->amount = $totalAmount;
            $payment->paid_at = now();
            $payment->payment_note = "Paiement groupé des commissions";
            $payment->save();
            
            // Mettre à jour toutes les commissions
            foreach ($pendingCommissions as $commission) {
                $commission->paid_at = now();
                $commission->commission_payment_id = $payment->id;
                $commission->save();
            }
            
            DB::commit();
            
            return redirect()->back()->with('success', 'Toutes les commissions du vendeur ont été payées avec succès. Montant total: ' . $totalAmount);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erreur lors du paiement des commissions: ' . $e->getMessage());
        }
    }

    /**
     * Afficher les commissions par boutique avec statistiques par vendeur
     *
     * @param  int  $shopId
     * @return \Illuminate\Http\Response
     */
    public function shopCommissions($shopId)
    {
        // Vérifier les autorisations
        if (!auth()->user()->can('view commissions')) {
            abort(403, 'Accès non autorisé');
        }
        
        $shop = Shop::findOrFail($shopId);
        
        // Récupérer toutes les commissions de la boutique
        $commissions = Commission::where('shop_id', $shopId)
            ->with(['user', 'sale'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Statistiques par vendeur
        $vendorStats = $commissions->groupBy('user_id')
            ->map(function ($userCommissions, $userId) {
                $user = User::find($userId);
                return [
                    'user' => $user,
                    'total_commissions' => $userCommissions->count(),
                    'total_amount' => $userCommissions->sum('amount'),
                    'paid_amount' => $userCommissions->whereNotNull('paid_at')->sum('amount'),
                    'pending_amount' => $userCommissions->whereNull('paid_at')->sum('amount'),
                    'last_paid_at' => $userCommissions->whereNotNull('paid_at')->max('paid_at')
                ];
            });
        
        $totalStats = [
            'total_commissions' => $commissions->count(),
            'total_amount' => $commissions->sum('amount'),
            'paid_amount' => $commissions->whereNotNull('paid_at')->sum('amount'),
            'pending_amount' => $commissions->whereNull('paid_at')->sum('amount')
        ];
        
        return view('commissions.shop', compact('shop', 'commissions', 'vendorStats', 'totalStats'));
    }
} 
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
use Illuminate\Support\Facades\Log;

class CommissionController extends Controller
{
    /**
     * Affiche la liste des commissions
     */
    /**
     * Affiche la liste des commissions avec filtres et statistiques
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Vérifier les autorisations
        if (!Gate::allows('view-commissions')) {
            abort(403, 'Action non autorisée.');
        }

        // Initialiser la requête de base avec les relations nécessaires (eager loading)
        $query = Commission::with([
            'user:id,name,email',
        ]);

        // Appliquer les filtres
        // 1. Filtre par statut
        if ($request->filled('status')) {
            if ($request->status === 'pending') {
                $query->where('is_paid', false);
            } elseif ($request->status === 'paid') {
                $query->where('is_paid', true);
            }
        }

        // 2. Filtre par vendeur
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // 3. Filtre par boutique
        if ($request->filled('shop_id')) {
            $query->where('shop_id', $request->shop_id);
        }

        // 4. Filtre par mois/année
        if ($request->filled('month')) {
            $query->where('period_month', $request->month);

            // Si l'année est également spécifiée
            if ($request->filled('year')) {
                $query->where('period_year', $request->year);
            }
        }

        // 5. Filtre par période (dates)
        if ($request->filled('period_start')) {
            $query->where('created_at', '>=', $request->period_start);
        }

        if ($request->filled('period_end')) {
            $query->where('created_at', '<=', $request->period_end);
        }

        // Obtenir les commissions paginées avec tri par date la plus récente
        $commissions = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString(); // Garde les paramètres de requête dans la pagination

        // Récupérer les boutiques selon les permissions de l'utilisateur
        $shops = Gate::allows('admin')
            ? Shop::orderBy('name')->get()
            : Auth::user()->shops()->orderBy('name')->get();

        // Récupérer la liste des vendeurs pour le filtre
        $sellers = User::where('role', 'vendeur')
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        // Préparer les données des boutiques avec leurs statistiques
        foreach ($shops as $shop) {
            // Optimisation: Utilisation de requêtes agrégées
            $shopStats = Commission::where('shop_id', $shop->id)
                ->selectRaw('SUM(amount) as total,
                                SUM(CASE WHEN is_paid = 0 THEN amount ELSE 0 END) as pending,
                                SUM(CASE WHEN is_paid = 1 THEN amount ELSE 0 END) as paid')
                ->first();

            $shop->commission_stats = [
                'total' => $shopStats->total ?? 0,
                'pending' => $shopStats->pending ?? 0,
                'paid' => $shopStats->paid ?? 0,
            ];

            // Obtenir le nombre de vendeurs associés à cette boutique
            if (!isset($shop->vendors_count)) {
                $shop->vendors_count = DB::table('shop_user')
                    ->where('shop_id', $shop->id)
                    ->where('is_manager', false)
                    ->count();
            }
        }

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

        // Statistiques globales
        // Optimisation: Utilisation d'une seule requête pour toutes les statistiques
        $globalStats = Commission::selectRaw('
        SUM(amount) as total_commissions,
        SUM(CASE WHEN is_paid = 0 THEN amount ELSE 0 END) as pending_commissions,
        SUM(CASE WHEN is_paid = 1 THEN amount ELSE 0 END) as paid_commissions,
        COUNT(*) as total_count,
        SUM(CASE WHEN is_paid = 0 THEN 1 ELSE 0 END) as pending_count,
        SUM(CASE WHEN is_paid = 1 THEN 1 ELSE 0 END) as paid_count
    ')->first();

        $stats = [
            'total_commissions' => $globalStats->total_commissions ?? 0,
            'pending_commissions' => $globalStats->pending_commissions ?? 0,
            'paid_commissions' => $globalStats->paid_commissions ?? 0,
            'total_count' => $globalStats->total_count ?? 0,
            'pending_count' => $globalStats->pending_count ?? 0,
            'paid_count' => $globalStats->paid_count ?? 0,
        ];

        // Renvoyer la vue avec toutes les données nécessaires
        return view('commissions.index', compact(
            'commissions',
            'sellers',
            'shops',
            'stats',
            'months'
        ));
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
            ->selectRaw('strftime("%Y", date) as year, strftime("%m", date) as month, SUM(total) as total, COUNT(*) as count')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        $monthlyCommissions = Commission::where('user_id', $user->id)
            ->selectRaw('strftime("%Y", created_at) as year, strftime("%m", created_at) as month, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        return view('commissions.vendor-report', compact('user', 'commissions', 'stats', 'monthlySales', 'monthlyCommissions'));
    }

    /**
     * Display the pending commissions for a specific vendor.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function vendorPendingReport(User $user)
    {
        // Vendor can only view their own commissions
        if (Auth::user()->role === 'vendeur' && Auth::id() !== $user->id) {
            abort(403);
        }

        $pendingCommissions = Commission::with(['bill', 'bill.client', 'shop'])
            ->where('user_id', $user->id)
            ->where('is_paid', false)
            ->orderBy('created_at', 'desc')
            ->get();

        $totalAmount = $pendingCommissions->sum('amount');

        // Calculate stats for the view
        $stats = [
            'total_commissions' => Commission::where('user_id', $user->id)->sum('amount'),
            'pending_commissions' => Commission::where('user_id', $user->id)->where('is_paid', false)->sum('amount'),
            'paid_commissions' => Commission::where('user_id', $user->id)->where('is_paid', true)->sum('amount'),
        ];

        return view('commissions.vendor-report', [
            'user' => $user,
            'commissions' => $pendingCommissions,
            'stats' => $stats,
            'status' => 'pending',
            'title' => 'Pending Commissions'
        ]);
    }

    public function vendorHistoryReport(User $user)
    {
        // Vendor can only view their own commissions
        if (Auth::user()->role === 'vendeur' && Auth::id() !== $user->id) {
            abort(403);
        }

        $paidCommissions = Commission::with(['bill', 'bill.client', 'shop'])
            ->where('user_id', $user->id)
            ->where('is_paid', true)
            ->orderBy('paid_at', 'desc')
            ->get();

        $totalAmount = $paidCommissions->sum('amount');

        // Calculate stats for the view
        $stats = [
            'total_commissions' => Commission::where('user_id', $user->id)->sum('amount'),
            'pending_commissions' => Commission::where('user_id', $user->id)->where('is_paid', false)->sum('amount'),
            'paid_commissions' => Commission::where('user_id', $user->id)->where('is_paid', true)->sum('amount'),
        ];

        return view('commissions.vendor-report', [
            'user' => $user,
            'commissions' => $paidCommissions,
            'stats' => $stats,
            'status' => 'paid',
            'title' => 'Commission History'
        ]);
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

        $commission->load([
            'user', 
            'bill.client', 
            'bill.items.product', 
            'shop',
            'payment'
        ]);

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
            : Auth::user()->shops()->get();

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
            : Auth::user()->shops()->get();

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
        // Vérifier l'existence de la boutique
        $shop = Shop::findOrFail($shopId);

        // Vérifier les autorisations
        if (!Gate::allows('view-shop-report', $shop)) {
            abort(403, 'Action non autorisée.');
        }

        // Obtenir les vendeurs associés à cette boutique
        $vendors = $shop->vendors;

        // Obtenir toutes les commissions pour chaque vendeur dans cette boutique
        $vendorStats = [];
        $totalPendingAmount = 0;
        $totalPaidAmount = 0;
        $totalAmount = 0;

        foreach ($vendors as $vendor) {
            $stats = $this->getVendorStats($vendor, $shopId);
            $vendorStats[$vendor->id] = $stats;

            $totalPendingAmount += $stats['pending_amount'];
            $totalPaidAmount += $stats['paid_amount'];
            $totalAmount += $stats['total_amount'];
        }

        // Statistiques globales
        $totalStats = [
            'pending_amount' => $totalPendingAmount,
            'paid_amount' => $totalPaidAmount,
            'total_amount' => $totalAmount,
        ];

        // Commissions en attente
        $pendingCommissions = Commission::with(['user', 'bill'])
            ->where('shop_id', $shopId)
            ->where('is_paid', false)
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'pending_page');

        // Toutes les commissions pour la vue compacte
        $allCommissions = Commission::with(['user', 'bill'])
            ->where('shop_id', $shopId)
            ->orderBy('created_at', 'desc')
            ->paginate(15, ['*'], 'all_page');

        // Paiements récents
        $recentPayments = CommissionPayment::with(['vendor'])
            ->where('shop_id', $shopId)
            ->orderBy('paid_at', 'desc')
            ->take(10)
            ->get();

        return view('commissions.shop-report', [
            'shop' => $shop,
            'vendors' => $vendors,
            'vendorStats' => $vendorStats,
            'totalStats' => $totalStats,
            'pendingCommissions' => $pendingCommissions,
            'recentPayments' => $recentPayments,
            'allCommissions' => $allCommissions
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
                        ->where('is_paid', true)
                        ->sum('amount');

        // Calculer le montant des commissions en attente
        $pendingAmount = Commission::where('user_id', $vendor->id)
                            ->whereHas('bill', function($query) use ($shopId) {
                                $query->where('shop_id', $shopId);
                            })
                            ->where('is_paid', false)
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
        if (!Gate::allows('pay-commissions')) {
            abort(403, 'Accès non autorisé');
        }

        $commission = Commission::findOrFail($commissionId);

        // Vérifier que la commission n'est pas déjà payée
        if ($commission->is_paid) {
            return redirect()->back()->with('error', 'Cette commission a déjà été payée.');
        }

        DB::beginTransaction();

        try {
            // Créer un nouveau paiement
            $payment = CommissionPayment::create([
                'reference' => CommissionPayment::generateReference(),
                'shop_id' => $commission->shop_id,
                'user_id' => $commission->user_id,
                'paid_by' => Auth::id(),
                'amount' => $commission->amount,
                'payment_method' => 'cash', // Default payment method
                'payment_reference' => null,
                'notes' => "Paiement individuel",
                'paid_at' => now(),
            ]);

            // Mettre à jour la commission
            $commission->update([
                'is_paid' => true,
                'paid_at' => now(),
                'paid_by' => Auth::id(),
                'payment_method' => 'cash',
                'payment_reference' => null,
                'payment_group_id' => $payment->id,
                'payment_notes' => "Paiement individuel",
            ]);

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
    public function payVendorCommissions(User $user)
    {
        // Only admin can pay commissions
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        // Get all unpaid commissions for this user
        $pendingCommissions = Commission::where('user_id', $user->id)
            ->where('is_paid', false)
            ->get();

        // If no pending commissions, redirect back
        if ($pendingCommissions->isEmpty()) {
            return redirect()->route('commissions.pending', $user)
                ->with('error', 'No pending commissions to pay.');
        }

        // Mark all as paid
        $now = now();
        foreach ($pendingCommissions as $commission) {
            $commission->update([
                'is_paid' => true,
                'paid_at' => $now
            ]);
        }

        // Redirect with success message
        return redirect()->route('commissions.pending', $user)
            ->with('success', 'All pending commissions have been marked as paid.');
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
        if (!Gate::allows('view-commissions')) {
            abort(403, 'Accès non autorisé');
        }

        $shop = Shop::findOrFail($shopId);

        // Récupérer toutes les commissions de la boutique
        $commissions = Commission::where('shop_id', $shopId)
            ->with(['user', 'bill'])
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
                    'paid_amount' => $userCommissions->where('is_paid', true)->sum('amount'),
                    'pending_amount' => $userCommissions->where('is_paid', false)->sum('amount'),
                    'last_paid_at' => $userCommissions->where('is_paid', true)->max('paid_at')
                ];
            });

        $totalStats = [
            'total_commissions' => $commissions->count(),
            'total_amount' => $commissions->sum('amount'),
            'paid_amount' => $commissions->where('is_paid', true)->sum('amount'),
            'pending_amount' => $commissions->where('is_paid', false)->sum('amount')
        ];

        return view('commissions.shop', compact('shop', 'commissions', 'vendorStats', 'totalStats'));
    }

    /**
     * Récupérer les commissions en attente d'un vendeur pour une boutique spécifique (API)
     *
     * @param  int  $userId
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * Récupérer les commissions en attente d'un vendeur pour le paiement en masse (API)
     *
     * @param  int  $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPendingCommissionsForPayment($userId, Request $request)
    {
        try {
            // Validation basique
            $shopId = $request->query('shop_id');
            if (!$shopId) {
                return response()->json(['error' => 'Shop ID is required'], 400);
            }

            // Requête simple sans vérifications d'autorisation
            $commissions = Commission::where('user_id', $userId)
                ->where('shop_id', $shopId)
                ->where('is_paid', false)
                ->get(['id', 'amount', 'created_at']);

            return response()->json([
                'commissions' => $commissions,
                'total' => $commissions->sum('amount')
            ]);
        } catch (\Exception $e) {
            // Log l'erreur pour que vous puissiez la déboguer
            Log::error('Error in getPendingCommissionsForPayment: ' . $e->getMessage());

            // Retourne une réponse d'erreur propre
            return response()->json([
                'error' => 'Une erreur est survenue lors de la récupération des commissions',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}

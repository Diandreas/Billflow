<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Shop;
use App\Models\Commission;
use App\Models\Bill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

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
            $query->where('status', $request->input('status'));
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

        // Statistiques
        $stats = [
            'total_commissions' => Commission::sum('amount'),
            'pending_commissions' => Commission::where('status', 'pending')->sum('amount'),
            'paid_commissions' => Commission::where('status', 'paid')->sum('amount'),
        ];

        return view('commissions.index', compact('commissions', 'sellers', 'shops', 'stats'));
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
            'pending_commissions' => Commission::where('user_id', $user->id)->where('status', 'pending')->sum('amount'),
            'paid_commissions' => Commission::where('user_id', $user->id)->where('status', 'paid')->sum('amount'),
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
        if (!auth()->user()->isAdmin() && !auth()->user()->isManager()) {
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

        if ($status) {
            $commissionsQuery->where('status', $status);
        }

        // Restreindre pour les managers
        if (auth()->user()->isManager() && !auth()->user()->isAdmin()) {
            $managedShopIds = auth()->user()->managedShops()->pluck('shops.id')->toArray();
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
                    $commission->status,
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
            'notes' => 'nullable|string',
        ]);

        $commission->update([
            'status' => 'paid',
            'paid_at' => now(),
            'description' => $commission->description . "\n" . ($validated['notes'] ?? '')
        ]);

        return redirect()->route('commissions.show', $commission)
            ->with('success', 'Commission marquée comme payée');
    }
} 
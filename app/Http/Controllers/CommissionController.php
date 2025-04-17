<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Shop;
use App\Models\Commission;
use App\Models\Bill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CommissionController extends Controller
{
    /**
     * Affiche la liste des commissions
     */
    public function index(Request $request)
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

        // Filtre par vendeur
        if ($vendorId) {
            $commissionsQuery->where('user_id', $vendorId);
        }

        // Filtre par boutique (via les factures)
        if ($shopId) {
            $commissionsQuery->whereHas('bill', function($query) use ($shopId) {
                $query->where('shop_id', $shopId);
            });
        }

        // Filtre par statut
        if ($status) {
            $commissionsQuery->where('status', $status);
        }

        // Les managers ne peuvent voir que les commissions des vendeurs de leurs boutiques
        if (auth()->user()->isManager() && !auth()->user()->isAdmin()) {
            $managedShopIds = auth()->user()->managedShops()->pluck('shops.id')->toArray();
            $commissionsQuery->whereHas('bill', function($query) use ($managedShopIds) {
                $query->whereIn('shop_id', $managedShopIds);
            });
        }

        // Récupérer les données paginées
        $commissions = $commissionsQuery->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Données pour les filtres
        $vendors = User::where('role', 'vendeur')->orderBy('name')->get();
        
        // Les shops dépendent du rôle de l'utilisateur
        if (auth()->user()->isAdmin()) {
            $shops = Shop::orderBy('name')->get();
        } else {
            $shops = auth()->user()->managedShops;
        }

        // Statistiques récapitulatives
        $stats = [
            'total_commissions' => $commissionsQuery->sum('amount'),
            'paid_commissions' => $commissionsQuery->where('status', 'paid')->sum('amount'),
            'pending_commissions' => $commissionsQuery->where('status', 'pending')->sum('amount'),
            'total_count' => $commissionsQuery->count(),
        ];

        return view('commissions.index', compact(
            'commissions',
            'vendors',
            'shops',
            'stats',
            'vendorId',
            'shopId',
            'startDate',
            'endDate',
            'status'
        ));
    }

    /**
     * Affiche le rapport de commissions d'un vendeur
     */
    public function vendorReport(User $vendor)
    {
        // Vérifier l'autorisation
        if (!auth()->user()->isAdmin() && !auth()->user()->isManager()) {
            if (auth()->id() !== $vendor->id) {
                return redirect()->route('dashboard')
                    ->with('error', 'Vous n\'avez pas les permissions nécessaires.');
            }
        }

        // Pour les managers, vérifier qu'ils ont accès à ce vendeur
        if (auth()->user()->isManager() && !auth()->user()->isAdmin() && auth()->id() !== $vendor->id) {
            $managedShopIds = auth()->user()->managedShops()->pluck('shops.id')->toArray();
            $vendorShopIds = $vendor->shops()->pluck('shops.id')->toArray();
            
            if (empty(array_intersect($managedShopIds, $vendorShopIds))) {
                return redirect()->route('dashboard')
                    ->with('error', 'Ce vendeur n\'appartient pas à l\'une de vos boutiques.');
            }
        }

        // Dates par défaut (mois en cours)
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        // Commissions du mois
        $commissions = Commission::with(['bill'])
            ->where('user_id', $vendor->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->get();

        // Regrouper par source (type de commission)
        $bySource = $commissions->groupBy('source')
            ->map(function ($items) {
                return [
                    'count' => $items->count(),
                    'total' => $items->sum('amount'),
                ];
            });

        // Regrouper par boutique
        $byShop = $commissions->groupBy(function ($commission) {
                return $commission->bill ? $commission->bill->shop_id : 'N/A';
            })
            ->map(function ($items, $shopId) {
                $shopName = 'N/A';
                if ($shopId !== 'N/A') {
                    $shop = Shop::find($shopId);
                    $shopName = $shop ? $shop->name : 'N/A';
                }
                
                return [
                    'name' => $shopName,
                    'count' => $items->count(),
                    'total' => $items->sum('amount'),
                ];
            });

        // Regrouper par statut
        $byStatus = $commissions->groupBy('status')
            ->map(function ($items) {
                return [
                    'count' => $items->count(),
                    'total' => $items->sum('amount'),
                ];
            });

        // Historique sur les 12 derniers mois
        $monthlyHistory = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $start = $month->copy()->startOfMonth();
            $end = $month->copy()->endOfMonth();
            
            $monthTotal = Commission::where('user_id', $vendor->id)
                ->whereBetween('created_at', [$start, $end])
                ->sum('amount');
                
            $monthlyHistory[] = [
                'month' => $month->format('M Y'),
                'total' => $monthTotal,
            ];
        }

        // Données récapitulatives
        $summary = [
            'total_commissions' => $commissions->sum('amount'),
            'paid_commissions' => $commissions->where('status', 'paid')->sum('amount'),
            'pending_commissions' => $commissions->where('status', 'pending')->sum('amount'),
            'total_count' => $commissions->count(),
        ];

        // Boutiques du vendeur
        $shops = $vendor->shops;

        return view('commissions.vendor-report', compact(
            'vendor',
            'commissions',
            'bySource',
            'byShop',
            'byStatus',
            'monthlyHistory',
            'summary',
            'shops',
            'startDate',
            'endDate'
        ));
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
     * Marque plusieurs commissions comme payées
     */
    public function markAsPaid(Request $request)
    {
        // Vérifier l'autorisation
        if (!auth()->user()->isAdmin() && !auth()->user()->isManager()) {
            return redirect()->route('commissions.index')
                ->with('error', 'Vous n\'avez pas les permissions nécessaires.');
        }

        $validated = $request->validate([
            'commission_ids' => 'required|array',
            'commission_ids.*' => 'exists:commissions,id',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string',
            'payment_reference' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        // Récupérer les commissions
        $commissions = Commission::whereIn('id', $validated['commission_ids'])->get();
        
        // Vérifier que les managers n'accèdent qu'aux commissions de leurs boutiques
        if (auth()->user()->isManager() && !auth()->user()->isAdmin()) {
            $managedShopIds = auth()->user()->managedShops()->pluck('shops.id')->toArray();
            
            foreach ($commissions as $commission) {
                if ($commission->bill && !in_array($commission->bill->shop_id, $managedShopIds)) {
                    return redirect()->route('commissions.index')
                        ->with('error', 'Vous n\'avez pas accès à certaines commissions sélectionnées.');
                }
            }
        }

        // Mettre à jour les commissions
        foreach ($commissions as $commission) {
            $commission->status = 'paid';
            $commission->payment_date = $validated['payment_date'];
            $commission->payment_method = $validated['payment_method'];
            $commission->payment_reference = $validated['payment_reference'];
            $commission->payment_notes = $validated['notes'];
            $commission->save();
        }

        return redirect()->route('commissions.index')
            ->with('success', count($commissions) . ' commissions ont été marquées comme payées.');
    }
} 
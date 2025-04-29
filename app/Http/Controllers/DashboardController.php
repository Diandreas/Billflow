<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Product;
use App\Models\Client;
use App\Models\Shop;
use App\Models\User;
use App\Models\Supplier;
use App\Models\InventoryMovement;
use App\Models\CommissionPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class DashboardController extends Controller
{
    /**
     * Récupérer les statistiques des factures avec support des plages de dates personnalisées
     */
    public function getStats(Request $request)
    {
        try {
            $timeRange = $request->input('timeRange', 'month');
            $metric = $request->input('metric', 'count');
            $shopId = $request->input('shop_id');

            // Gestion des dates personnalisées
            if ($timeRange === 'custom') {
                $startDate = $request->input('startDate') ? Carbon::parse($request->input('startDate')) : now()->startOfMonth();
                $endDate = $request->input('endDate') ? Carbon::parse($request->input('endDate')) : now();
            } else {
                // Définir les dates de début et de fin en fonction de la période sélectionnée
                $startDate = match ($timeRange) {
                    'month' => now()->startOfMonth(),
                    'quarter' => now()->startOfQuarter(),
                    'year' => now()->startOfYear(),
                    default => now()->startOfMonth()
                };
                $endDate = now();
            }

            // Vérifier et ajuster si la date de début est postérieure à la date de fin
            if ($startDate > $endDate) {
                [$startDate, $endDate] = [$endDate, $startDate];
            }

            // Requête pour récupérer les statistiques
            $query = DB::table('bills')
                ->select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('COUNT(*) as count'),
                    DB::raw('COALESCE(SUM(total), 0) as amount')
                )
                ->whereBetween('created_at', [$startDate, $endDate]);

            // Filtrer par boutique si demandé
            if ($shopId) {
                $query->where('shop_id', $shopId);
            }

            $results = $query->groupBy(DB::raw('DATE(created_at)'))
                ->orderBy('date')
                ->get();

            // Remplir les dates manquantes
            $filledData = [];
            $currentDate = Carbon::parse($startDate);

            while ($currentDate <= $endDate) {
                $dateStr = $currentDate->format('Y-m-d');
                $existingData = $results->firstWhere('date', $dateStr);

                $filledData[] = [
                    'date' => $currentDate->format('d/m/Y'),
                    'count' => $existingData ? $existingData->count : 0,
                    'amount' => $existingData ? floatval($existingData->amount) : 0,
                ];

                $currentDate->addDay();
            }

            return response()->json($filledData);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer les données détaillées du mois en cours et précédent
     */
    public function getDashboardData(Request $request)
    {
        try {
            $currentMonth = now()->format('m');
            $currentYear = now()->format('Y');
            $shopId = $request->input('shop_id');

            // Requête de base
            $query = DB::table('bills');

            // Filtrer par boutique si demandé
            if ($shopId) {
                $query->where('shop_id', $shopId);
            }

            // Statistiques du mois en cours
            $currentMonthStats = (clone $query)
                ->select(
                    DB::raw('COUNT(*) as count'),
                    DB::raw('COALESCE(SUM(total), 0) as total'),
                    DB::raw('COALESCE(AVG(total), 0) as average')
                )
                ->whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $currentMonth)
                ->first();

            // Statistiques du mois précédent
            $lastMonth = now()->subMonth();
            $lastMonthStats = (clone $query)
                ->select(
                    DB::raw('COUNT(*) as count'),
                    DB::raw('COALESCE(SUM(total), 0) as total'),
                    DB::raw('COALESCE(AVG(total), 0) as average')
                )
                ->whereYear('created_at', $lastMonth->format('Y'))
                ->whereMonth('created_at', $lastMonth->format('m'))
                ->first();

            // Calcul de la croissance
            $growth = 0;
            if ($lastMonthStats->total > 0) {
                $growth = (($currentMonthStats->total - $lastMonthStats->total) / $lastMonthStats->total) * 100;
            }

            return response()->json([
                'currentMonth' => [
                    'count' => $currentMonthStats->count,
                    'total' => number_format($currentMonthStats->total, 0, ',', ' ') . ' FCFA',
                    'average' => number_format($currentMonthStats->average, 0, ',', ' ') . ' FCFA'
                ],
                'lastMonth' => [
                    'count' => $lastMonthStats->count,
                    'total' => number_format($lastMonthStats->total, 0, ',', ' ') . ' FCFA',
                    'average' => number_format($lastMonthStats->average, 0, ',', ' ') . ' FCFA'
                ],
                'growth' => round($growth, 1)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Page principale du tableau de bord
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $shopId = $request->input('shop_id');
        $shops = null;
        $selectedShop = null;

        // Si c'est un admin ou manager, on peut sélectionner une boutique
        if (Gate::allows('manager', $user)) {
            if (Gate::allows('admin', $user)) {
                $shops = Shop::where('is_active', true)->get();
            } else {
                $shops = $user->managedShops;
            }

            // Si un ID de boutique est fourni, on récupère cette boutique
            if ($shopId) {
                $selectedShop = Shop::with('managers')->find($shopId);
            }
        } elseif (Gate::allows('vendeur', $user)) {
            // Si c'est un vendeur, on utilise automatiquement sa première boutique
            $userShops = $user->shops;
            if ($userShops->count() > 0) {
                $shopId = $userShops->first()->id;
                $selectedShop = $userShops->first();
                $shops = $userShops;
            }
        }

        // Préparation des requêtes avec filtrage par boutique au besoin
        $billsQuery = Bill::query();

        if ($shopId) {
            $billsQuery->where('shop_id', $shopId);
        }

        // Calcul du pourcentage de changement mensuel pour les factures
        $thisMonth = now()->month;
        $lastMonth = now()->subMonth()->month;
        $thisYear = now()->year;
        $lastYear = now()->subMonth()->year;

        $currentMonthBills = (clone $billsQuery)
            ->whereYear('created_at', $thisYear)
            ->whereMonth('created_at', $thisMonth)
            ->count();

        $lastMonthBills = (clone $billsQuery)
            ->whereYear('created_at', $lastYear)
            ->whereMonth('created_at', $lastMonth)
            ->count();

        $monthlyBillsPercentChange = 0;
        if ($lastMonthBills > 0) {
            $monthlyBillsPercentChange = round((($currentMonthBills - $lastMonthBills) / $lastMonthBills) * 100, 1);
        }

        // Statistiques globales
        $globalStats = [
            'totalBills' => $billsQuery->count(),
            'monthlyBills' => $currentMonthBills,
            'monthlyBillsPercentChange' => $monthlyBillsPercentChange,
            'totalRevenue' => number_format($billsQuery->sum('total'), 0, ',', ' ') . ' FCFA',
            'averageTicket' => number_format($billsQuery->avg('total') ?? 0, 0, ',', ' ') . ' FCFA'
        ];

        // Statistiques des paiements de commissions (pour admin et manager)
        if (Gate::allows('manager', $user)) {
            $commissionsPaymentsQuery = CommissionPayment::query();

            if ($shopId) {
                $commissionsPaymentsQuery->where('shop_id', $shopId);
            }

            $currentMonthPayments = (clone $commissionsPaymentsQuery)
                ->whereYear('paid_at', $thisYear)
                ->whereMonth('paid_at', $thisMonth)
                ->count();

            $lastMonthPayments = (clone $commissionsPaymentsQuery)
                ->whereYear('paid_at', $lastYear)
                ->whereMonth('paid_at', $lastMonth)
                ->count();

            $monthlyPaymentsPercentChange = 0;
            if ($lastMonthPayments > 0) {
                $monthlyPaymentsPercentChange = round((($currentMonthPayments - $lastMonthPayments) / $lastMonthPayments) * 100, 1);
            }

            $globalStats['commissionsPayments'] = [
                'totalPayments' => $commissionsPaymentsQuery->count(),
                'totalAmount' => number_format($commissionsPaymentsQuery->sum('amount'), 0, ',', ' ') . ' FCFA',
                'monthlyPayments' => $currentMonthPayments,
                'monthlyPaymentsPercentChange' => $monthlyPaymentsPercentChange,
            ];
        }

        // Dernières factures
        $latestBills = (clone $billsQuery)
            ->with('client')
            ->latest()
            ->take(5)
            ->get();

        // Derniers paiements de commissions (pour admin et manager)
        $latestCommissionPayments = null;
        if (Gate::allows('manager', $user)) {
            $commissionPaymentsQuery = \App\Models\CommissionPayment::query();

            if ($shopId) {
                $commissionPaymentsQuery->where('shop_id', $shopId);
            }

            $latestCommissionPayments = $commissionPaymentsQuery
                ->with(['user', 'shop'])
                ->latest('paid_at')
                ->take(5)
                ->get();
        }

        // Requête de base pour les tops clients
        $topClientsQuery = DB::table('bills')
            ->join('clients', 'bills.client_id', '=', 'clients.id')
            ->select(
                'clients.id',
                'clients.name',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total) as total')
            );

        if ($shopId) {
            $topClientsQuery->where('bills.shop_id', $shopId);
        }

        // Top clients
        $topClients = $topClientsQuery
            ->groupBy('clients.id', 'clients.name')
            ->orderByDesc('total')
            ->take(5)
            ->get()
            ->map(function ($client) use ($shopId) {
                $client->total = number_format($client->total, 0, ',', ' ') . ' FCFA';
                // Ajouter l'URL pour voir les détails du client
                $client->url = route('clients.show', $client->id);
                return $client;
            });

        // Statistiques des vendeurs de la boutique sélectionnée
        $sellerStats = [];
        if ($shopId) {
            $sellerStats = User::whereHas('shops', function ($q) use ($shopId) {
                    $q->where('shops.id', $shopId);
                })
                ->where('role', 'vendeur')
                ->withCount(['sales' => function ($q) use ($shopId) {
                    $q->where('shop_id', $shopId);
                }])
                ->with(['sales' => function ($q) use ($shopId) {
                    $q->where('shop_id', $shopId);
                }])
                ->get()
                ->map(function ($seller) {
                    $salesTotal = $seller->sales->sum('total');

                    return [
                        'id' => $seller->id,
                        'name' => $seller->name,
                        'sales_count' => $seller->sales_count,
                        'sales_total' => number_format($salesTotal, 0, ',', ' ') . ' FCFA',
                        'commission' => number_format($salesTotal * ($seller->commission_rate / 100), 0, ',', ' ') . ' FCFA',
                        'commission_rate' => $seller->commission_rate . '%',
                    ];
                });
        }

        // Données des produits les plus vendus (pour le donut chart)
        $topProductsQuery = DB::table('bill_items')
            ->join('products', 'bill_items.product_id', '=', 'products.id')
            ->join('bills', 'bill_items.bill_id', '=', 'bills.id')
            ->select(
                'products.id',
                'products.name',
                DB::raw('SUM(bill_items.quantity) as quantity'),
                DB::raw('SUM(bill_items.total) as total')
            );

        if ($shopId) {
            $topProductsQuery->where('bills.shop_id', $shopId);
        }

        $topProducts = $topProductsQuery
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('quantity')
            ->take(5)
            ->get()
            ->map(function ($product) {
                $product->total = number_format($product->total, 0, ',', ' ') . ' FCFA';
                // Ajouter l'URL pour voir les détails du produit
                $product->url = route('products.show', $product->id);
                return $product;
            });

        // Données pour le graphique de performance des ventes
        $salesChartData = $this->getSalesPerformanceData($shopId);

        // Données pour le graphique du statut des factures
        $billStatusData = $this->getBillStatusData($shopId);

        // Données pour le graphique des méthodes de paiement
        $paymentMethodsData = $this->getPaymentMethodsData($shopId);

        // Si c'est un vendeur, on récupère ses statistiques de commissions et de paiements
        if (Gate::allows('vendeur', $user)) {
            // Statistiques de commissions
            $vendorCommissions = \App\Models\Commission::where('user_id', $user->id);

            if ($shopId) {
                $vendorCommissions->where('shop_id', $shopId);
            }

            // Statistiques des ventes du vendeur
            $vendorSales = Bill::where('user_id', $user->id);

            if ($shopId) {
                $vendorSales->where('shop_id', $shopId);
            }

            $totalSales = $vendorSales->count();
            $totalSalesAmount = $vendorSales->sum('total');

            // Nombre de ventes ce mois-ci
            $currentMonthSales = (clone $vendorSales)
                ->whereYear('created_at', $thisYear)
                ->whereMonth('created_at', $thisMonth)
                ->count();

            // Nombre de ventes le mois dernier
            $lastMonthSales = (clone $vendorSales)
                ->whereYear('created_at', $lastYear)
                ->whereMonth('created_at', $lastMonth)
                ->count();

            // Calcul du pourcentage de changement
            $salesPercentChange = 0;
            if ($lastMonthSales > 0) {
                $salesPercentChange = round((($currentMonthSales - $lastMonthSales) / $lastMonthSales) * 100, 1);
            }

            // Les dernières factures du vendeur
            $latestBills = (clone $vendorSales)->with('client')->latest()->take(5)->get();

            $vendorStats = [
                'total_sales' => $totalSales,
                'total_sales_amount' => number_format($totalSalesAmount, 0, ',', ' ') . ' FCFA',
                'monthly_sales' => $currentMonthSales,
                'monthly_sales_percent_change' => $salesPercentChange,
                'commission_rate' => $user->commission_rate . '%',
                'total_commissions' => $vendorCommissions->count(),
                'total_amount' => number_format($vendorCommissions->sum('amount'), 0, ',', ' ') . ' FCFA',
                'paid_commissions' => $vendorCommissions->where('is_paid', true)->count(),
                'pending_commissions' => $vendorCommissions->where('is_paid', false)->count(),
            ];

            // Statistiques de paiements reçus
            $vendorPayments = \App\Models\CommissionPayment::where('user_id', $user->id);

            if ($shopId) {
                $vendorPayments->where('shop_id', $shopId);
            }

            $vendorPaymentsStats = [
                'total_payments' => $vendorPayments->count(),
                'total_received' => number_format($vendorPayments->sum('amount'), 0, ',', ' ') . ' FCFA',
                'last_payment' => $vendorPayments->latest('paid_at')->first(),
            ];

            // Les 5 derniers paiements reçus
            $vendorLatestPayments = $vendorPayments->with(['shop'])
                ->latest('paid_at')
                ->take(5)
                ->get();

            return view('dashboard', compact(
                'selectedShop',
                'shops',
                'globalStats',
                'latestBills',
                'salesPercentChange',
                'vendorStats',
                'vendorPaymentsStats',
                'vendorLatestPayments'
            ));
        }

        return view('dashboard', compact(
            'shops',
            'selectedShop',
            'globalStats',
            'latestBills',
            'topClients',
            'topProducts',
            'sellerStats',
            'salesChartData',
            'billStatusData',
            'paymentMethodsData',
            'latestCommissionPayments'
        ));
    }

    /**
     * Récupérer les données pour le graphique de performance des ventes
     */
    protected function getSalesPerformanceData($shopId = null)
    {
        // Dates pour le mois actuel et le mois précédent
        $now = Carbon::now();
        $currentMonthStart = $now->copy()->startOfMonth();
        $lastMonthStart = $now->copy()->subMonth()->startOfMonth();
        $lastMonthEnd = $now->copy()->subMonth()->endOfMonth();

        // Labels pour les semaines
        $labels = [];
        $currentWeekLabels = [];
        $daysInMonth = $now->daysInMonth;

        for ($i = 1; $i <= 4; $i++) {
            $weekStart = ($i - 1) * 7 + 1;
            $weekEnd = min($i * 7, $daysInMonth);
            $labels[] = "Sem $i ($weekStart-$weekEnd)";
            $currentWeekLabels[] = $weekStart;
        }

        // Données des ventes pour le mois actuel
        $currentMonthData = $this->getWeeklySalesData($currentMonthStart, $now, $shopId);

        // Données des ventes pour le mois précédent
        $previousMonthData = $this->getWeeklySalesData($lastMonthStart, $lastMonthEnd, $shopId);

        return [
            'labels' => $labels,
            'current' => $currentMonthData,
            'previous' => $previousMonthData
        ];
    }

    /**
     * Récupérer les données de ventes hebdomadaires pour une période donnée
     */
    private function getWeeklySalesData($startDate, $endDate, $shopId = null)
    {
        $daysInMonth = $startDate->daysInMonth;
        $weeklySales = [0, 0, 0, 0]; // 4 semaines

        $query = Bill::whereBetween('date', [$startDate, $endDate]);

        if ($shopId) {
            $query->where('shop_id', $shopId);
        }

        $bills = $query->get();

        foreach ($bills as $bill) {
            $day = $bill->date->day;
            $weekIndex = min(floor(($day - 1) / 7), 3); // Déterminer la semaine (0-3)
            $weeklySales[$weekIndex] += $bill->total;
        }

        return $weeklySales;
    }

    /**
     * Récupérer les données pour le graphique du statut des factures
     */
    protected function getBillStatusData($shopId = null)
    {
        $query = Bill::select('status', DB::raw('count(*) as total'))
            ->whereIn('status', ['pending', 'paid', 'cancelled'])
            ->groupBy('status');

        if ($shopId) {
            $query->where('shop_id', $shopId);
        }

        $results = $query->get();

        $statusLabels = [];
        $statusValues = [];

        // Mapper les statuts pour la traduction
        $statusMap = [
            'pending' => 'En attente',
            'paid' => 'Payée',
            'cancelled' => 'Annulée'
        ];

        foreach ($results as $result) {
            $statusLabels[] = $statusMap[$result->status] ?? $result->status;
            $statusValues[] = $result->total;
        }

        // Ajouter des valeurs par défaut si aucune donnée n'est trouvée
        if (empty($statusLabels)) {
            $statusLabels = ['Payée', 'En attente', 'Annulée'];
            $statusValues = [0, 0, 0];
        }

        return [
            'labels' => $statusLabels,
            'values' => $statusValues
        ];
    }

    /**
     * Récupérer les données pour le graphique des méthodes de paiement
     */
    protected function getPaymentMethodsData($shopId = null)
    {
        $query = Bill::select('payment_method', DB::raw('count(*) as total'))
            ->whereNotNull('payment_method')
            ->where('status', 'paid')
            ->groupBy('payment_method');

        if ($shopId) {
            $query->where('shop_id', $shopId);
        }

        $results = $query->get();

        $methodLabels = [];
        $methodValues = [];

        // Mapper les méthodes pour la traduction
        $methodMap = [
            'cash' => 'Espèces',
            'card' => 'Carte',
            'mobile_money' => 'Mobile Money',
            'transfer' => 'Virement',
            'check' => 'Chèque'
        ];

        foreach ($results as $result) {
            $methodLabels[] = $methodMap[$result->payment_method] ?? $result->payment_method;
            $methodValues[] = $result->total;
        }

        // Ajouter des valeurs par défaut si aucune donnée n'est trouvée
        if (empty($methodLabels)) {
            $methodLabels = ['Espèces', 'Carte', 'Mobile Money', 'Virement'];
            $methodValues = [0, 0, 0, 0];
        }

        return [
            'labels' => $methodLabels,
            'values' => $methodValues
        ];
    }

    /**
     * Récupérer les données pour le graphique de comparaison des revenus
     */
    public function getRevenueComparison(Request $request)
    {
        try {
            $currentMonth = now();
            $previousMonth = now()->subMonth();
            $shopId = $request->input('shop_id');

            // Divisez le mois en 4 semaines
            $weeks = [
                [
                    'start' => $currentMonth->copy()->startOfMonth(),
                    'end' => $currentMonth->copy()->startOfMonth()->addDays(6)
                ],
                [
                    'start' => $currentMonth->copy()->startOfMonth()->addDays(7),
                    'end' => $currentMonth->copy()->startOfMonth()->addDays(13)
                ],
                [
                    'start' => $currentMonth->copy()->startOfMonth()->addDays(14),
                    'end' => $currentMonth->copy()->startOfMonth()->addDays(20)
                ],
                [
                    'start' => $currentMonth->copy()->startOfMonth()->addDays(21),
                    'end' => $currentMonth->copy()->endOfMonth()
                ]
            ];

            $prevWeeks = [
                [
                    'start' => $previousMonth->copy()->startOfMonth(),
                    'end' => $previousMonth->copy()->startOfMonth()->addDays(6)
                ],
                [
                    'start' => $previousMonth->copy()->startOfMonth()->addDays(7),
                    'end' => $previousMonth->copy()->startOfMonth()->addDays(13)
                ],
                [
                    'start' => $previousMonth->copy()->startOfMonth()->addDays(14),
                    'end' => $previousMonth->copy()->startOfMonth()->addDays(20)
                ],
                [
                    'start' => $previousMonth->copy()->startOfMonth()->addDays(21),
                    'end' => $previousMonth->copy()->endOfMonth()
                ]
            ];

            // Récupérer les données pour les graphiques
            $currentWeeklyData = [];
            $previousWeeklyData = [];

            foreach ($weeks as $index => $weekDates) {
                $query = DB::table('bills')
                    ->select(DB::raw('COALESCE(SUM(total), 0) as revenue'))
                    ->whereBetween('created_at', [$weekDates['start'], $weekDates['end']]);

                if ($shopId) {
                    $query->where('shop_id', $shopId);
                }

                $currentWeeklyData[] = $query->first()->revenue;
            }

            foreach ($prevWeeks as $index => $weekDates) {
                $query = DB::table('bills')
                    ->select(DB::raw('COALESCE(SUM(total), 0) as revenue'))
                    ->whereBetween('created_at', [$weekDates['start'], $weekDates['end']]);

                if ($shopId) {
                    $query->where('shop_id', $shopId);
                }

                $previousWeeklyData[] = $query->first()->revenue;
            }

            $labels = ['Semaine 1', 'Semaine 2', 'Semaine 3', 'Semaine 4'];

            return response()->json([
                'labels' => $labels,
                'currentMonth' => $currentWeeklyData,
                'lastMonth' => $previousWeeklyData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer les données pour le graphique de statut des factures
     */
    public function getInvoiceStatus(Request $request)
    {
        try {
            $shopId = $request->input('shop_id');

            $query = DB::table('bills')
                ->select('status', DB::raw('COUNT(*) as count'))
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year);

            if ($shopId) {
                $query->where('shop_id', $shopId);
            }

            $statusCounts = $query->groupBy('status')->get();

            $labels = [];
            $counts = [];
            $colors = [
                'pending' => '#FFC107',
                'paid' => '#28A745',
                'partially_paid' => '#17A2B8',
                'cancelled' => '#DC3545',
                'overdue' => '#FF5722'
            ];
            $backgroundColors = [];

            foreach ($statusCounts as $status) {
                $statusLabel = match ($status->status) {
                    'pending' => 'En attente',
                    'paid' => 'Payée',
                    'partially_paid' => 'Partiellement payée',
                    'cancelled' => 'Annulée',
                    'overdue' => 'En retard',
                    default => $status->status
                };

                $labels[] = $statusLabel;
                $counts[] = $status->count;
                $backgroundColors[] = $colors[$status->status] ?? '#6C757D';
            }

            return response()->json([
                'labels' => $labels,
                'counts' => $counts,
                'colors' => $backgroundColors
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer les statistiques d'inventaire
     */
    public function getInventoryStats(Request $request)
    {
        try {
            $shopId = $request->input('shop_id');

            // Produits à faible stock
            $lowStockQuery = DB::table('products')
                ->where('quantity', '<=', DB::raw('reorder_level'))
                ->where('quantity', '>', 0);

            // Produits en rupture de stock
            $outOfStockQuery = DB::table('products')
                ->where('quantity', '=', 0);

            // Récupérer les mouvements d'inventaire récents
            $inventoryMovementsQuery = InventoryMovement::with(['product', 'user'])
                ->latest()
                ->take(5);

            // Filtrer par boutique si nécessaire
            if ($shopId) {
                $inventoryMovementsQuery->where('shop_id', $shopId);
            }

            $result = [
                'lowStockCount' => $lowStockQuery->count(),
                'outOfStockCount' => $outOfStockQuery->count(),
                'recentMovements' => $inventoryMovementsQuery->get()
            ];

            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Méthode optionnelle pour obtenir un aperçu rapide des statistiques
     */
    public function quickStats()
    {
        try {
            $thisWeek = now()->startOfWeek();
            $thisMonth = now()->startOfMonth();

            $weekStats = DB::table('bills')
                ->select(
                    DB::raw('COUNT(*) as count'),
                    DB::raw('COALESCE(SUM(total), 0) as total')
                )
                ->where('created_at', '>=', $thisWeek)
                ->first();

            $monthStats = DB::table('bills')
                ->select(
                    DB::raw('COUNT(*) as count'),
                    DB::raw('COALESCE(SUM(total), 0) as total')
                )
                ->where('created_at', '>=', $thisMonth)
                ->first();

            return response()->json([
                'weekly' => [
                    'count' => $weekStats->count,
                    'total' => number_format($weekStats->total, 0, ',', ' ') . ' FCFA'
                ],
                'monthly' => [
                    'count' => $monthStats->count,
                    'total' => number_format($monthStats->total, 0, ',', ' ') . ' FCFA'
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Exporter les statistiques au format CSV
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportStats()
    {
        $stats = $this->getStatsData();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=statistiques-export.csv',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($stats) {
            $file = fopen('php://output', 'w');

            // En-têtes CSV - Statistiques générales
            fputcsv($file, ['Statistiques Générales']);
            fputcsv($file, ['Métrique', 'Valeur']);
            fputcsv($file, ['Total Factures', $stats['totalBills']]);
            fputcsv($file, ['Factures ce mois', $stats['monthlyBills']]);
            fputcsv($file, ['Revenu Total', $stats['totalRevenue']]);
            fputcsv($file, ['Panier Moyen', $stats['averageTicket']]);
            fputcsv($file, ['']);

            // En-têtes CSV - Top clients
            fputcsv($file, ['Top 5 Clients']);
            fputcsv($file, ['Client', 'Nombre de factures', 'Montant total']);

            foreach ($stats['topClients'] as $client) {
                fputcsv($file, [$client->name, $client->count, $client->total]);
            }

            fputcsv($file, ['']);

            // En-têtes CSV - Statistiques mensuelles
            fputcsv($file, ['Statistiques Mensuelles']);
            fputcsv($file, ['Mois', 'Nombre de factures', 'Revenu Total']);

            foreach ($stats['monthlyStats'] as $month => $data) {
                fputcsv($file, [
                    $month,
                    $data['count'],
                    number_format($data['amount'], 0, ',', ' ') . ' FCFA'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Récupérer les données complètes pour l'exportation des statistiques
     */
    private function getStatsData()
    {
        // Statistiques globales
        $stats = [
            'totalBills' => Bill::count(),
            'monthlyBills' => Bill::whereMonth('created_at', now()->month)->count(),
            'totalRevenue' => number_format(Bill::sum('total'), 0, ',', ' ') . ' FCFA',
            'averageTicket' => number_format(Bill::avg('total') ?? 0, 0, ',', ' ') . ' FCFA'
        ];

        // Top clients
        $stats['topClients'] = DB::table('bills')
            ->join('clients', 'bills.client_id', '=', 'clients.id')
            ->select(
                'clients.name',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total) as total')
            )
            ->groupBy('clients.name')
            ->orderByDesc('total')
            ->take(5)
            ->get()
            ->map(function ($client) {
                $client->total = number_format($client->total, 0, ',', ' ') . ' FCFA';
                return $client;
            });

        // Statistiques mensuelles (6 derniers mois)
        $monthlyStats = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthStr = $month->format('Y-m');
            $monthLabel = $month->translatedFormat('F Y');

            $bills = Bill::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->get();

            $monthlyStats[$monthLabel] = [
                'count' => $bills->count(),
                'amount' => $bills->sum('total')
            ];
        }

        $stats['monthlyStats'] = $monthlyStats;

        return $stats;
    }

    /**
     * Récupère les statistiques des meilleurs fournisseurs
     */
    public function getTopSuppliers(Request $request)
    {
        try {
            $timeRange = $request->input('timeRange', 'month');
            $limit = $request->input('limit', 5);
            $shopId = $request->input('shop_id');

            // Gestion des dates
            $startDate = match ($timeRange) {
                'month' => now()->startOfMonth(),
                'quarter' => now()->startOfQuarter(),
                'year' => now()->startOfYear(),
                'all' => now()->subYears(50), // Toutes les données
                default => now()->startOfMonth()
            };
            $endDate = now();

            // Requête de base pour les statistiques de vente par fournisseur
            $query = DB::table('suppliers')
                ->join('products', 'suppliers.id', '=', 'products.supplier_id')
                ->join('bill_items', 'products.id', '=', 'bill_items.product_id')
                ->join('bills', 'bill_items.bill_id', '=', 'bills.id')
                ->where('bills.status', '!=', 'cancelled')
                ->where('bills.date', '>=', $startDate)
                ->where('bills.date', '<=', $endDate)
                ->select(
                    'suppliers.id',
                    'suppliers.name',
                    DB::raw('COUNT(DISTINCT bills.id) as bills_count'),
                    DB::raw('SUM(bill_items.quantity) as products_sold'),
                    DB::raw('SUM(bill_items.quantity * bill_items.price) as total_sales')
                )
                ->groupBy('suppliers.id', 'suppliers.name')
                ->orderByDesc('total_sales');

            // Filtrer par boutique si spécifié
            if ($shopId) {
                $query->where('bills.shop_id', $shopId);
            }

            // Top fournisseurs par ventes
            $topSuppliersBySales = $query->limit($limit)->get();

            // Top fournisseurs par produits en stock
            $topSuppliersByStock = Supplier::withCount('products')
                ->join('products', 'suppliers.id', '=', 'products.supplier_id')
                ->where('products.type', 'physical')
                ->select('suppliers.*', DB::raw('SUM(products.stock_quantity) as total_stock'))
                ->groupBy('suppliers.id')
                ->orderByDesc('total_stock')
                ->limit($limit)
                ->get();

            // Top fournisseurs par valeur de stock
            $topSuppliersByStockValue = Supplier::withCount('products')
                ->join('products', 'suppliers.id', '=', 'products.supplier_id')
                ->where('products.type', 'physical')
                ->select(
                    'suppliers.*', 
                    DB::raw('SUM(products.stock_quantity * products.cost_price) as stock_value')
                )
                ->groupBy('suppliers.id')
                ->orderByDesc('stock_value')
                ->limit($limit)
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'top_suppliers_by_sales' => $topSuppliersBySales,
                    'top_suppliers_by_stock' => $topSuppliersByStock,
                    'top_suppliers_by_stock_value' => $topSuppliersByStockValue
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des meilleurs fournisseurs: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des données: ' . $e->getMessage()
            ], 500);
        }
    }
}

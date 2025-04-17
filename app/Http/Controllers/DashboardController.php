<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Product;
use App\Models\Client;
use App\Models\Shop;
use App\Models\User;
use App\Models\InventoryMovement;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

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
        
        // Si c'est un admin ou manager, on peut sélectionner une boutique
        if ($user->isAdmin() || $user->isManager()) {
            if ($user->isAdmin()) {
                $shops = Shop::where('is_active', true)->get();
            } else {
                $shops = $user->managedShops;
            }
        } elseif ($user->isVendeur()) {
            // Si c'est un vendeur, on utilise automatiquement sa première boutique
            $userShops = $user->shops;
            if ($userShops->count() > 0) {
                $shopId = $userShops->first()->id;
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

        // Dernières factures
        $latestBills = (clone $billsQuery)
            ->with('client')
            ->latest()
            ->take(5)
            ->get();

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
                
                // Calculer la tendance réelle des achats du client
                $currentMonthQuery = Bill::where('client_id', $client->id)
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
                    
                $lastMonthQuery = Bill::where('client_id', $client->id)
                    ->whereMonth('created_at', now()->subMonth()->month)
                    ->whereYear('created_at', now()->subMonth()->year);
                
                if ($shopId) {
                    $currentMonthQuery->where('shop_id', $shopId);
                    $lastMonthQuery->where('shop_id', $shopId);
                }
                
                $clientBillsCurrentMonth = $currentMonthQuery->sum('total');
                $clientBillsLastMonth = $lastMonthQuery->sum('total');
                
                $client->trend = 0;
                if ($clientBillsLastMonth > 0) {
                    $client->trend = round((($clientBillsCurrentMonth - $clientBillsLastMonth) / $clientBillsLastMonth) * 100, 1);
                }
                
                return $client;
            });
            
        // Si c'est une boutique spécifique, obtenez ses informations
        $selectedShop = null;
        if ($shopId) {
            $selectedShop = Shop::with(['managers', 'vendors'])->find($shopId);
        }

        // Statistiques spécifiques aux vendeurs pour la boutique sélectionnée
        $sellerStats = null;
        if ($shopId) {
            $sellerStats = User::with(['sales' => function($query) use ($shopId) {
                    $query->where('shop_id', $shopId)
                          ->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year);
                }])
                ->whereHas('shops', function($query) use ($shopId) {
                    $query->where('shops.id', $shopId);
                })
                ->where('role', 'vendeur')
                ->get()
                ->map(function($seller) {
                    return [
                        'id' => $seller->id,
                        'name' => $seller->name,
                        'sales_count' => $seller->sales->count(),
                        'sales_total' => number_format($seller->sales->sum('total'), 0, ',', ' ') . ' FCFA',
                        'commission' => number_format($seller->sales->sum('total') * ($seller->commission_rate / 100), 0, ',', ' ') . ' FCFA'
                    ];
                });
        }

        return view('dashboard', compact('globalStats', 'latestBills', 'topClients', 'shops', 'selectedShop', 'sellerStats'));
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
}

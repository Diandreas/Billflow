<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Product;
use App\Models\Client;
use App\Models\InventoryMovement;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
            $results = DB::table('bills')
                ->select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('COUNT(*) as count'),
                    DB::raw('COALESCE(SUM(total), 0) as amount')
                )
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy(DB::raw('DATE(created_at)'))
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
    public function getDashboardData()
    {
        try {
            $currentMonth = now()->format('m');
            $currentYear = now()->format('Y');

            // Statistiques du mois en cours
            $currentMonthStats = DB::table('bills')
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
            $lastMonthStats = DB::table('bills')
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
    public function index()
    {
        // Calcul du pourcentage de changement mensuel pour les factures
        $thisMonth = now()->month;
        $lastMonth = now()->subMonth()->month;
        $thisYear = now()->year;
        $lastYear = now()->subMonth()->year;
        
        $currentMonthBills = Bill::whereYear('created_at', $thisYear)
            ->whereMonth('created_at', $thisMonth)
            ->count();
            
        $lastMonthBills = Bill::whereYear('created_at', $lastYear)
            ->whereMonth('created_at', $lastMonth)
            ->count();
        
        $monthlyBillsPercentChange = 0;
        if ($lastMonthBills > 0) {
            $monthlyBillsPercentChange = round((($currentMonthBills - $lastMonthBills) / $lastMonthBills) * 100, 1);
        }

        // Statistiques globales
        $globalStats = [
            'totalBills' => Bill::count(),
            'monthlyBills' => $currentMonthBills,
            'monthlyBillsPercentChange' => $monthlyBillsPercentChange,
            'totalRevenue' => number_format(Bill::sum('total'), 0, ',', ' ') . ' FCFA',
            'averageTicket' => number_format(Bill::avg('total') ?? 0, 0, ',', ' ') . ' FCFA'
        ];

        // Dernières factures
        $latestBills = Bill::with('client')
            ->latest()
            ->take(5)
            ->get();

        // Top clients
        $topClients = DB::table('bills')
            ->join('clients', 'bills.client_id', '=', 'clients.id')
            ->select(
                'clients.id',
                'clients.name',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total) as total')
            )
            ->groupBy('clients.id', 'clients.name')
            ->orderByDesc('total')
            ->take(5)
            ->get()
            ->map(function ($client) {
                $client->total = number_format($client->total, 0, ',', ' ') . ' FCFA';
                
                // Calculer la tendance réelle des achats du client
                $clientBillsCurrentMonth = Bill::where('client_id', $client->id)
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->sum('total');
                
                $clientBillsLastMonth = Bill::where('client_id', $client->id)
                    ->whereMonth('created_at', now()->subMonth()->month)
                    ->whereYear('created_at', now()->subMonth()->year)
                    ->sum('total');
                
                $client->trend = 0;
                if ($clientBillsLastMonth > 0) {
                    $client->trend = round((($clientBillsCurrentMonth - $clientBillsLastMonth) / $clientBillsLastMonth) * 100, 1);
                }
                
                return $client;
            });

        return view('dashboard', compact('globalStats', 'latestBills', 'topClients'));
    }

    /**
     * Récupérer les données pour le graphique de comparaison des revenus
     */
    public function getRevenueComparison()
    {
        try {
            $currentMonth = now();
            $previousMonth = now()->subMonth();
            
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
            
            $currentData = [];
            $previousData = [];
            $labels = [];
            
            foreach ($weeks as $index => $week) {
                $revenue = Bill::whereBetween('created_at', [$week['start'], $week['end']])->sum('total');
                $currentData[] = $revenue;
                $labels[] = 'Semaine ' . ($index + 1);
            }
            
            foreach ($prevWeeks as $week) {
                $revenue = Bill::whereBetween('created_at', [$week['start'], $week['end']])->sum('total');
                $previousData[] = $revenue;
            }
            
            return response()->json([
                'labels' => $labels,
                'currentData' => $currentData,
                'previousData' => $previousData
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
    public function getInvoiceStatus()
    {
        try {
            $statusCounts = Bill::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->status => $item->count];
                })
                ->toArray();
            
            // Assurer la présence des statuts communs même s'ils sont à zéro
            $statuses = ['Payé', 'En attente', 'Annulé'];
            $data = [];
            $labels = [];
            
            foreach ($statuses as $status) {
                $labels[] = $status;
                $data[] = $statusCounts[$status] ?? 0;
            }
            
            return response()->json([
                'labels' => $labels,
                'data' => $data
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Récupérer les statistiques d'inventaire pour le dashboard
     */
    public function getInventoryStats()
    {
        try {
            // Produits avec stock bas (sous le seuil d'alerte)
            $lowStockProducts = Product::whereRaw('stock_quantity <= stock_alert_threshold')
                ->where('type', 'physique')
                ->where('stock_alert_threshold', '>', 0)
                ->count();
            
            // Mouvements d'inventaire récents
            $recentMovements = InventoryMovement::with(['product'])
                ->latest()
                ->take(5)
                ->get();
            
            // Valeur totale du stock
            $stockValue = Product::where('type', 'physique')
                ->whereNotNull('cost_price')
                ->select(DB::raw('SUM(stock_quantity * cost_price) as total_value'))
                ->first()
                ->total_value ?? 0;
            
            return response()->json([
                'lowStockProducts' => $lowStockProducts,
                'recentMovements' => $recentMovements,
                'stockValue' => number_format($stockValue, 0, ',', ' ') . ' FCFA'
            ]);
            
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

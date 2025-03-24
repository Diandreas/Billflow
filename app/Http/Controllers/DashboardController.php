<?php

namespace App\Http\Controllers;

use App\Models\Bill;
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

            // Ajoutez cette ligne pour déboguer
            Log::info('Filled Data:', $filledData);

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
        // Statistiques globales
        $globalStats = [
            'totalBills' => Bill::count(),
            'monthlyBills' => Bill::whereMonth('created_at', now()->month)->count(),
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
                $client->trend = rand(-10, 10);
                return $client;
            });

        return view('dashboard', compact('globalStats', 'latestBills', 'topClients'));
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
            fputcsv($file, ['Total Clients', $stats['totalClients']]);
            fputcsv($file, ['Total Factures', $stats['totalBills']]);
            fputcsv($file, ['Total Revenus', $stats['totalRevenue']]);
            fputcsv($file, ['Panier Moyen', $stats['averageBill']]);
            fputcsv($file, ['Clients ce mois', $stats['clientsThisMonth']]);
            fputcsv($file, ['Revenus ce mois', $stats['revenueThisMonth']]);
            
            // Ligne vide pour séparer les sections
            fputcsv($file, []);
            
            // Statistiques mensuelles
            fputcsv($file, ['Statistiques Mensuelles']);
            fputcsv($file, ['Mois', 'Clients', 'Factures', 'Revenus']);
            
            foreach ($stats['monthlyStats'] as $month => $data) {
                fputcsv($file, [
                    $month,
                    $data['clients'] ?? 0,
                    $data['bills'] ?? 0,
                    $data['revenue'] ?? 0
                ]);
            }
            
            // Ligne vide pour séparer les sections
            fputcsv($file, []);
            
            // Top clients
            fputcsv($file, ['Top Clients']);
            fputcsv($file, ['Nom', 'Total Factures', 'Total Dépensé']);
            
            foreach ($stats['topClients'] as $client) {
                fputcsv($file, [
                    $client['name'] ?? 'Client inconnu',
                    $client['bills_count'] ?? 0,
                    $client['total_spent'] ?? 0
                ]);
            }
            
            // Ligne vide pour séparer les sections
            fputcsv($file, []);
            
            // Produits les plus vendus
            fputcsv($file, ['Produits les plus vendus']);
            fputcsv($file, ['Nom', 'Quantité vendue', 'Revenus générés']);
            
            foreach ($stats['topProducts'] as $product) {
                fputcsv($file, [
                    $product['name'] ?? 'Produit inconnu',
                    $product['quantity_sold'] ?? 0,
                    $product['revenue'] ?? 0
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Obtenir les données de statistiques pour l'exportation
     */
    private function getStatsData()
    {
        // Statistiques globales
        $totalClients = \App\Models\Client::count();
        $totalBills = \App\Models\Bill::count();
        $totalRevenue = \App\Models\Bill::sum('total');
        $averageBill = $totalBills > 0 ? \App\Models\Bill::avg('total') : 0;
        
        // Statistiques du mois en cours
        $currentMonth = now()->startOfMonth();
        $clientsThisMonth = \App\Models\Client::where('created_at', '>=', $currentMonth)->count();
        $revenueThisMonth = \App\Models\Bill::where('date', '>=', $currentMonth)->sum('total');
        
        // Statistiques mensuelles (12 derniers mois)
        $monthlyStats = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();
            $monthKey = $month->format('Y-m');
            
            $monthlyStats[$monthKey] = [
                'clients' => \App\Models\Client::whereBetween('created_at', [$monthStart, $monthEnd])->count(),
                'bills' => \App\Models\Bill::whereBetween('date', [$monthStart, $monthEnd])->count(),
                'revenue' => \App\Models\Bill::whereBetween('date', [$monthStart, $monthEnd])->sum('total')
            ];
        }
        
        // Top clients
        $topClients = \App\Models\Client::withCount('bills')
            ->withSum('bills as total_spent', 'total')
            ->orderByDesc('total_spent')
            ->limit(10)
            ->get()
            ->map(function($client) {
                return [
                    'name' => $client->name,
                    'bills_count' => $client->bills_count,
                    'total_spent' => $client->total_spent
                ];
            });
        
        // Top produits
        $topProducts = \App\Models\Product::withCount(['bills as quantity_sold' => function($query) {
                $query->select(\Illuminate\Support\Facades\DB::raw('SUM(bill_product.quantity)'));
            }])
            ->withCount(['bills as revenue' => function($query) {
                $query->select(\Illuminate\Support\Facades\DB::raw('SUM(bill_product.quantity * bill_product.price)'));
            }])
            ->orderByDesc('revenue')
            ->limit(10)
            ->get()
            ->map(function($product) {
                return [
                    'name' => $product->name,
                    'quantity_sold' => $product->quantity_sold,
                    'revenue' => $product->revenue
                ];
            });
        
        return [
            'totalClients' => $totalClients,
            'totalBills' => $totalBills,
            'totalRevenue' => $totalRevenue,
            'averageBill' => $averageBill,
            'clientsThisMonth' => $clientsThisMonth,
            'revenueThisMonth' => $revenueThisMonth,
            'monthlyStats' => $monthlyStats,
            'topClients' => $topClients,
            'topProducts' => $topProducts
        ];
    }
}

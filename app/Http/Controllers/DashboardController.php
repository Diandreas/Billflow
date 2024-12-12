<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
                    'amount' => $existingData ? $existingData->amount : 0,
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
}

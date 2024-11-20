<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function getStats(Request $request)
    {
        $timeRange = $request->input('timeRange', 'month');
        $metric = $request->input('metric', 'count');
        $groupBy = $request->input('groupBy', 'day');

        $startDate = match($timeRange) {
            'month' => now()->subDays(30),
            'quarter' => now()->subMonths(3),
            'year' => now()->subYear(),
            default => now()->subDays(30)
        };

        $dateFormat = match($groupBy) {
            'day' => '%Y-%m-%d',
            'week' => '%Y-%U',
            'month' => '%Y-%m',
            default => '%Y-%m-%d'
        };

        $query = Bill::query()
            ->where('date', '>=', $startDate)
            ->select(
                DB::raw("DATE_FORMAT(date, '$dateFormat') as date"),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total) as amount'),
                DB::raw('AVG(total) as avgTicket')
            )
            ->groupBy('date')
            ->orderBy('date');

        // Ajout des données manquantes pour avoir une série complète
        $data = $query->get();
        $filledData = $this->fillMissingDates($data, $startDate, $groupBy);

        return response()->json($filledData);
    }

    private function fillMissingDates($data, $startDate, $groupBy)
    {
        $filledData = [];
        $currentDate = $startDate;
        $endDate = now();
        $interval = match($groupBy) {
            'day' => 'P1D',
            'week' => 'P1W',
            'month' => 'P1M',
            default => 'P1D'
        };

        $dateFormat = match($groupBy) {
            'day' => 'Y-m-d',
            'week' => 'Y-W',
            'month' => 'Y-m',
            default => 'Y-m-d'
        };

        $period = new \DatePeriod(
            $currentDate,
            new \DateInterval($interval),
            $endDate
        );

        $dataByDate = $data->keyBy('date');

        foreach ($period as $date) {
            $dateKey = $date->format($dateFormat);
            $existingData = $dataByDate[$dateKey] ?? null;

            $filledData[] = [
                'date' => $dateKey,
                'count' => $existingData?->count ?? 0,
                'amount' => $existingData?->amount ?? 0,
                'avgTicket' => $existingData?->avgTicket ?? 0
            ];
        }

        return $filledData;
    }

    public function getDashboardData()
    {
        // Récupération des statistiques globales
        $globalStats = [
            'totalBills' => Bill::count(),
            'monthlyBills' => Bill::whereMonth('date', now()->month)->count(),
            'totalRevenue' => Bill::sum('total'),
            'monthlyRevenue' => Bill::whereMonth('date', now()->month)->sum('total'),
            'averageTicket' => Bill::avg('total'),
            'monthlyAverageTicket' => Bill::whereMonth('date', now()->month)->avg('total')
        ];

        // Évolution par rapport au mois précédent
        $lastMonthRevenue = Bill::whereMonth('date', now()->subMonth()->month)->sum('total');
        $currentMonthRevenue = $globalStats['monthlyRevenue'];

        $revenueGrowth = $lastMonthRevenue > 0
            ? (($currentMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue * 100)
            : 0;

        // Top clients
        $topClients = DB::table('bills')
            ->join('clients', 'bills.client_id', '=', 'clients.id')
            ->select('clients.name', DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as total'))
            ->groupBy('clients.id', 'clients.name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // Répartition des factures par montant
        $billsDistribution = [
            'small' => Bill::where('total', '<', 1000)->count(),
            'medium' => Bill::whereBetween('total', [1000, 5000])->count(),
            'large' => Bill::where('total', '>', 5000)->count(),
        ];

        // Évolution hebdomadaire
        $weeklyTrend = DB::table('bills')
            ->where('date', '>=', now()->subWeeks(4))
            ->select(
                DB::raw('YEAR(date) as year'),
                DB::raw('WEEK(date) as week'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total) as total')
            )
            ->groupBy('year', 'week')
            ->orderBy('year')
            ->orderBy('week')
            ->get();

        return response()->json([
            'globalStats' => $globalStats,
            'revenueGrowth' => $revenueGrowth,
            'topClients' => $topClients,
            'billsDistribution' => $billsDistribution,
            'weeklyTrend' => $weeklyTrend
        ]);
    }
}

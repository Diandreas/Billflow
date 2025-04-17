<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\Bill;
use App\Models\User;
use App\Models\Product;
use App\Models\InventoryMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ShopDashboardController extends Controller
{
    /**
     * Affiche le tableau de bord d'une boutique spécifique
     */
    public function show(Shop $shop)
    {
        // Vérifier que l'utilisateur a accès à cette boutique
        if (!auth()->user()->canAccessShop($shop->id)) {
            return redirect()->route('dashboard')
                ->with('error', 'Vous n\'avez pas accès à cette boutique.');
        }

        // Période (aujourd'hui, cette semaine, ce mois, cette année)
        $period = request('period', 'month');
        
        // Dates de début et fin selon la période
        $startDate = $this->getStartDate($period);
        $endDate = now();
        
        // Récupérer les données des ventes
        $salesData = $this->getSalesData($shop, $startDate, $endDate);
        
        // Récupérer les données de performance des vendeurs
        $vendorPerformance = $this->getVendorPerformance($shop, $startDate, $endDate);
        
        // Récupérer les données de stock
        $stockData = $this->getStockData($shop);
        
        return view('shops.dashboard', compact(
            'shop',
            'period',
            'salesData',
            'vendorPerformance',
            'stockData'
        ));
    }
    
    /**
     * Récupère la date de début selon la période demandée
     */
    private function getStartDate($period)
    {
        return match($period) {
            'day' => now()->startOfDay(),
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'year' => now()->startOfYear(),
            default => now()->startOfMonth(),
        };
    }
    
    /**
     * Récupère les données des ventes pour la période spécifiée
     */
    private function getSalesData(Shop $shop, $startDate, $endDate)
    {
        // Total des ventes
        $totalSales = Bill::where('shop_id', $shop->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->where('status', '<>', 'cancelled')
            ->sum('total');
            
        // Nombre de factures
        $billCount = Bill::where('shop_id', $shop->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->where('status', '<>', 'cancelled')
            ->count();
            
        // Valeur moyenne par facture
        $averageBillValue = $billCount > 0 ? ($totalSales / $billCount) : 0;
        
        // Ventes par catégorie de produits
        $salesByCategory = Bill::join('bill_items', 'bills.id', '=', 'bill_items.bill_id')
            ->join('products', 'bill_items.product_id', '=', 'products.id')
            ->join('product_categories', 'products.category_id', '=', 'product_categories.id')
            ->where('bills.shop_id', $shop->id)
            ->whereBetween('bills.date', [$startDate, $endDate])
            ->where('bills.status', '<>', 'cancelled')
            ->groupBy('product_categories.id', 'product_categories.name')
            ->select(
                'product_categories.id',
                'product_categories.name',
                DB::raw('SUM(bill_items.quantity * bill_items.price) as total_sales')
            )
            ->orderBy('total_sales', 'desc')
            ->get();
            
        // Ventes par jour (pour le graphique)
        $salesByDay = Bill::where('shop_id', $shop->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->where('status', '<>', 'cancelled')
            ->groupBy(DB::raw('DATE(date)'))
            ->select(
                DB::raw('DATE(date) as sale_date'),
                DB::raw('SUM(total) as daily_total')
            )
            ->orderBy('sale_date')
            ->get();
            
        // Formater pour le graphique
        $salesChartData = [
            'labels' => $salesByDay->pluck('sale_date')->map(function ($date) {
                return Carbon::parse($date)->format('d/m');
            })->toArray(),
            'data' => $salesByDay->pluck('daily_total')->toArray(),
        ];
        
        // Produits les plus vendus
        $topProducts = Bill::join('bill_items', 'bills.id', '=', 'bill_items.bill_id')
            ->join('products', 'bill_items.product_id', '=', 'products.id')
            ->where('bills.shop_id', $shop->id)
            ->whereBetween('bills.date', [$startDate, $endDate])
            ->where('bills.status', '<>', 'cancelled')
            ->groupBy('products.id', 'products.name')
            ->select(
                'products.id',
                'products.name',
                DB::raw('SUM(bill_items.quantity) as total_quantity'),
                DB::raw('SUM(bill_items.quantity * bill_items.price) as total_sales')
            )
            ->orderBy('total_quantity', 'desc')
            ->limit(10)
            ->get();
            
        return [
            'totalSales' => $totalSales,
            'billCount' => $billCount,
            'averageBillValue' => $averageBillValue,
            'salesByCategory' => $salesByCategory,
            'salesChartData' => $salesChartData,
            'topProducts' => $topProducts,
        ];
    }
    
    /**
     * Récupère les données de performance des vendeurs
     */
    private function getVendorPerformance(Shop $shop, $startDate, $endDate)
    {
        // Récupérer les vendeurs de la boutique
        $vendors = $shop->vendors;
        $vendorIds = $vendors->pluck('id')->toArray();
        
        // Ventes par vendeur
        $salesByVendor = Bill::where('shop_id', $shop->id)
            ->whereIn('seller_id', $vendorIds)
            ->whereBetween('date', [$startDate, $endDate])
            ->where('status', '<>', 'cancelled')
            ->groupBy('seller_id')
            ->select(
                'seller_id',
                DB::raw('COUNT(*) as bill_count'),
                DB::raw('SUM(total) as total_sales')
            )
            ->get()
            ->keyBy('seller_id');
            
        // Nombre de produits vendus par vendeur
        $productCountByVendor = Bill::join('bill_items', 'bills.id', '=', 'bill_items.bill_id')
            ->where('bills.shop_id', $shop->id)
            ->whereIn('seller_id', $vendorIds)
            ->whereBetween('bills.date', [$startDate, $endDate])
            ->where('bills.status', '<>', 'cancelled')
            ->groupBy('seller_id')
            ->select(
                'seller_id',
                DB::raw('SUM(bill_items.quantity) as total_products')
            )
            ->get()
            ->keyBy('seller_id');
            
        // Préparer les données consolidées
        $vendorPerformance = [];
        foreach ($vendors as $vendor) {
            $vendorId = $vendor->id;
            $sales = $salesByVendor[$vendorId] ?? null;
            $products = $productCountByVendor[$vendorId] ?? null;
            
            $vendorPerformance[] = [
                'id' => $vendorId,
                'name' => $vendor->name,
                'sales' => $sales ? $sales->total_sales : 0,
                'billCount' => $sales ? $sales->bill_count : 0,
                'productCount' => $products ? $products->total_products : 0,
                'averageBillValue' => $sales && $sales->bill_count > 0 ? ($sales->total_sales / $sales->bill_count) : 0,
            ];
        }
        
        // Trier par montant total des ventes
        usort($vendorPerformance, function ($a, $b) {
            return $b['sales'] <=> $a['sales'];
        });
        
        return $vendorPerformance;
    }
    
    /**
     * Récupère les données de stock de la boutique
     */
    private function getStockData(Shop $shop)
    {
        // Produits avec un stock faible (sous le seuil d'alerte)
        $lowStockProducts = Product::where('stock', '<=', DB::raw('alert_threshold'))
            ->where('stock', '>', 0)
            ->where('alert_threshold', '>', 0)
            ->orderBy(DB::raw('stock / alert_threshold'))
            ->limit(10)
            ->get();
            
        // Produits en rupture de stock
        $outOfStockProducts = Product::where('stock', '<=', 0)
            ->where('is_active', true)
            ->limit(10)
            ->get();
            
        // Mouvements récents de stock
        $recentMovements = InventoryMovement::with(['product', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
            
        return [
            'lowStockProducts' => $lowStockProducts,
            'outOfStockProducts' => $outOfStockProducts,
            'recentMovements' => $recentMovements,
        ];
    }
} 
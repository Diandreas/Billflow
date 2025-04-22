<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

Route::middleware('auth')->group(function () {
    Route::get('/dashboard/stats', [DashboardController::class, 'getStats']);
    Route::get('/dashboard/data', [DashboardController::class, 'getDashboardData']);
    Route::get('/dashboard/revenue-comparison', [DashboardController::class, 'getRevenueComparison']);
    Route::get('/dashboard/invoice-status', [DashboardController::class, 'getInvoiceStatus']);
    Route::get('/dashboard/inventory-stats', [DashboardController::class, 'getInventoryStats']);

    Route::get('/products/{product}/price-history', function (Product $product) {
        $priceHistory = DB::table('bill_products')
            ->select('unit_price', DB::raw('COUNT(*) as usage_count'))
            ->where('product_id', $product->id)
            ->groupBy('unit_price')
            ->orderByDesc('usage_count')
            ->get();
    
        return response()->json($priceHistory);
    });

    // Route pour récupérer les vendeurs d'une boutique
    Route::get('/shops/{shop}/vendors', function (\App\Models\Shop $shop) {
        return $shop->users()
            ->where('role', 'vendeur')
            ->get(['id', 'name']);
    });
});

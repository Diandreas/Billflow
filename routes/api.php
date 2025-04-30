<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

// Dans routes/api.php
Route::middleware('auth:sanctum')->get('/dashboard/top-suppliers', [App\Http\Controllers\DashboardController::class, 'getTopSuppliers']);

Route::get('/commissions/{userId}', [App\Http\Controllers\CommissionController::class, 'getVendorPendingCommissions'])->middleware('auth:web');
Route::middleware('auth:web')->group(function () {

    Route::get('/dashboard/stats', [DashboardController::class, 'getStats']);
    Route::get('/dashboard/data', [DashboardController::class, 'getDashboardData']);
    Route::get('/dashboard/revenue-comparison', [DashboardController::class, 'getRevenueComparison']);
    Route::get('/dashboard/invoice-status', [DashboardController::class, 'getInvoiceStatus']);
    Route::get('/dashboard/inventory-stats', [DashboardController::class, 'getInventoryStats']);
    Route::get('/dashboard/top-suppliers', [DashboardController::class, 'getTopSuppliers']);
    Route::get('/dashboard/supplier-stats', function() {
        $suppliers = Supplier::withCount('products')
            ->withSum('products', 'stock_quantity')
            ->orderByDesc('products_count')
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $suppliers
        ]);
    });

    Route::get('/products/{product}/price-history', function (Product $product) {
        // Récupérer l'historique des prix avec le nombre d'utilisations
        $priceHistory = DB::table('bill_items')
            ->join('bills', 'bill_items.bill_id', '=', 'bills.id')
            ->select(
                'bill_items.price',
                DB::raw('COUNT(*) as usage_count'),
                DB::raw('SUM(bill_items.quantity) as total_quantity'),
                DB::raw('SUM(bill_items.quantity * bill_items.price) as total_amount'),
                DB::raw('MIN(bills.date) as first_used'),
                DB::raw('MAX(bills.date) as last_used')
            )
            ->where('bill_items.product_id', $product->id)
            ->where('bills.status', '!=', 'cancelled')
            ->groupBy('bill_items.price')
            ->orderByDesc('usage_count')
            ->get()
            ->map(function($item) {
                return [
                    'price' => $item->price,
                    'usage_count' => $item->usage_count,
                    'total_quantity' => $item->total_quantity,
                    'total_amount' => $item->total_amount,
                    'first_used' => $item->first_used ? date('Y-m-d', strtotime($item->first_used)) : null,
                    'last_used' => $item->last_used ? date('Y-m-d', strtotime($item->last_used)) : null,
                    'is_default' => $item->price == $product->default_price
                ];
            });

        return response()->json([
            'success' => true,
            'default_price' => $product->default_price,
            'data' => $priceHistory
        ]);
    });

    // Route pour récupérer les vendeurs d'une boutique
    Route::get('/shops/{shop}/vendors', function (\App\Models\Shop $shop) {
        return $shop->users()
            ->where('role', 'vendeur')
            ->get(['id', 'name']);
    });
});

<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/dashboard/stats', [DashboardController::class, 'getStats']);
    Route::get('/dashboard/data', [DashboardController::class, 'getDashboardData']);
    Route::get('/dashboard/revenue-comparison', [DashboardController::class, 'getRevenueComparison']);
    Route::get('/dashboard/invoice-status', [DashboardController::class, 'getInvoiceStatus']);
    Route::get('/dashboard/inventory-stats', [DashboardController::class, 'getInventoryStats']);
});

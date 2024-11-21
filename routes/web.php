<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('/');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/dashboard/stats', [DashboardController::class, 'getStats']);
    Route::get('/dashboard/data', [DashboardController::class, 'getDashboardData'])
        ->name('dashboard.data');
    // Routes pour les clients
    Route::resource('clients', ClientController::class);

    // Routes pour les produits
    Route::resource('products', ProductController::class);
//    Route::resource('products', ProductController::class);
    Route::get('/api/products/search', [ProductController::class, 'search'])->name('products.search');
    Route::post('/api/products/quick-create', [ProductController::class, 'quickCreate'])->name('products.quick-create');
    Route::get('/api/products/{product}/price-history', [ProductController::class, 'priceHistory'])->name('products.price-history');
    Route::get('/api/products/{product}/usage-stats', [ProductController::class, 'usageStats'])->name('products.usage-stats');

    // Routes pour les factures
    Route::resource('bills', BillController::class);

    // Routes pour les paramètres
    Route::get('language/{locale}', [LanguageController::class, 'switch'])->name('language.switch');
    Route::get('bills/{bill}/download', [BillController::class, 'downloadPdf'])->name('bills.download');

    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [SettingController::class, 'update'])->name('settings.update');
    Route::delete('settings/logo', [SettingController::class, 'deleteLogo'])->name('settings.delete-logo');
    Route::get('settings/backup', [SettingController::class, 'downloadBackup'])->name('settings.backup');
    Route::post('settings/restore', [SettingController::class, 'importBackup'])->name('settings.restore');
    Route::get('settings/preview', [SettingController::class, 'preview'])->name('settings.preview');
    Route::get('settings/defaults', [SettingController::class, 'getDefaultValues'])->name('settings.defaults');
});

require __DIR__.'/auth.php';

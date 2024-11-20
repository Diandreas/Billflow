<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Routes pour les clients
    Route::resource('clients', ClientController::class);

    // Routes pour les produits
    Route::resource('products', ProductController::class);

    // Routes pour les factures
    Route::resource('bills', BillController::class);

    // Routes pour les paramètres
    Route::resource('settings', SettingController::class)->only(['index', 'edit', 'update']);
});

require __DIR__.'/auth.php';

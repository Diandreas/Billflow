<?php

use App\Http\Controllers\BarterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\PhoneController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\VendorEquipmentController;
use App\Http\Controllers\ShopDashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CommissionController;
use App\Http\Controllers\CommissionPaymentController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ImportMapperController;

Route::get('/', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('/');
Route::get('/commissions/{userId}', [App\Http\Controllers\CommissionController::class, 'getVendorPendingCommissions']);
// Dans routes/web.php (pas routes/api.php)

Route::get('/get-pending-commissions/{userId}', [App\Http\Controllers\CommissionController::class, 'getPendingCommissionsForPayment']);
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
    Route::get('/dashboard/invoice-status', [DashboardController::class, 'getInvoiceStatus']);
    Route::get('/dashboard/revenue-comparison', [DashboardController::class, 'getRevenueComparison']);
    Route::get('/dashboard/inventory-stats', [DashboardController::class, 'getInventoryStats']);
    // Routes pour les clients
    Route::resource('clients', ClientController::class);
    Route::get('clients/{client}/bills', [ClientController::class, 'billsIndex'])->name('clients.bills.index');
    Route::get('clients/{client}/bills/create', [ClientController::class, 'billsCreate'])->name('clients.bills.create');
    Route::post('/clients/import', [ClientController::class, 'import'])->name('clients.import');
    Route::get('/templates/import-clients.csv', [ClientController::class, 'importTemplate'])->name('clients.import.template');

    // Routes pour les produits
    Route::resource('products', ProductController::class)->except(['show']);
    Route::get('/api/products/search', [ProductController::class, 'search'])->name('products.search');
    Route::post('/api/products/quick-create', [ProductController::class, 'quickCreate'])->name('products.quick-create');
    Route::get('/api/products/{product}/price-history', [ProductController::class, 'priceHistory'])->name('products.price-history');
    Route::get('/api/products/{product}/usage-stats', [ProductController::class, 'usageStats'])->name('products.usage-stats');
    Route::get('/clients/search', [ClientController::class, 'search']);
    Route::get('/products/search', [ProductController::class, 'search']);
    
    // Route pour l'exportation des produits - avant la route show pour éviter les conflits
    Route::get('/products/export', [ProductController::class, 'export'])->name('products.export');
    Route::get('/products-export', [ProductController::class, 'showExportForm'])->name('products.export.form');
    
    // Route de détail des produits - après les routes spécifiques
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
    
    // Nouvelles routes pour l'importation de produits
    Route::get('/products-import', [ImportMapperController::class, 'showImportForm'])->name('products.import.form');
    Route::post('/products-import', [ImportMapperController::class, 'analyzeFile'])->name('products.import');
    Route::post('/products-import/process-mapping', [ImportMapperController::class, 'processMapping'])->name('products.process-mapping');
    Route::get('/products-import/template', [ProductController::class, 'downloadTemplate'])->name('products.import.template');
    Route::get('/products-import/review', [ProductController::class, 'reviewImport'])->name('products.review-import');
    Route::post('/products-import/process-review', [ProductController::class, 'processReviewedImport'])->name('products.process-reviewed-import');

    // Routes pour les catégories de produits
    Route::resource('product-categories', ProductCategoryController::class);
    Route::get('/api/product-categories', [ProductCategoryController::class, 'getAll'])->name('api.product-categories');

    // Routes pour les fournisseurs
    Route::resource('suppliers', SupplierController::class);
    Route::get('/api/suppliers/search', [SupplierController::class, 'search'])->name('suppliers.search');
    Route::post('/api/suppliers/quick-create', [SupplierController::class, 'quickCreate'])->name('suppliers.quick-create');
    Route::get('/api/suppliers/stats', [SupplierController::class, 'getStats'])->name('suppliers.stats');

    // Routes pour la gestion de l'inventaire
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::get('/inventory/movements', [InventoryController::class, 'movements'])->name('inventory.movements');
    Route::get('/inventory/adjustment', [InventoryController::class, 'adjustment'])->name('inventory.adjustment');
    Route::post('/inventory/adjustment', [InventoryController::class, 'processAdjustment'])->name('inventory.process-adjustment');
    Route::get('/inventory/receive', [InventoryController::class, 'receive'])->name('inventory.receive');
    Route::post('/inventory/receive', [InventoryController::class, 'processReceive'])->name('inventory.process-receive');
    Route::get('/api/inventory/product/{productId}/stock', [InventoryController::class, 'getProductStock'])->name('api.inventory.product-stock');
    Route::patch('/inventory/{product}/adjust', [InventoryController::class, 'adjustSingle'])->name('inventory.adjust-single');

    // Routes pour les factures
    Route::resource('bills', BillController::class);
    Route::patch('bills/{bill}/status', [BillController::class, 'updateStatus'])->name('bills.update-status');
    Route::get('bills/{bill}/print', [BillController::class, 'print'])->name('bills.print');
    Route::post('bills/{bill}/signature', [BillController::class, 'addSignature'])->name('bills.signature');
    Route::get('bills/by-price/{price}', [BillController::class, 'byPrice'])->name('bills.by-price');
    Route::post('bills/{bill}/approve', [BillController::class, 'approve'])->name('bills.approve');
    Route::get('bills/verify/{code}', [BillController::class, 'verify'])->name('bills.verify');

    // Routes pour les boutiques
    Route::middleware(['auth'])->group(function () {
        Route::get('/shops', [ShopController::class, 'index'])->name('shops.index');
        Route::get('/shops/create', [ShopController::class, 'create'])->name('shops.create');
        Route::post('/shops', [ShopController::class, 'store'])->name('shops.store');
        Route::get('/shops/{shop}', [ShopController::class, 'show'])->name('shops.show');
        Route::get('/shops/{shop}/edit', [ShopController::class, 'edit'])->name('shops.edit');
        Route::put('/shops/{shop}', [ShopController::class, 'update'])->name('shops.update');
        Route::delete('/shops/{shop}', [ShopController::class, 'destroy'])->name('shops.destroy');
        Route::get('/shops/{shop}/manage-users', [ShopController::class, 'manageUsers'])->name('shops.manage-users');
        Route::post('/shops/{shop}/assign-users', [ShopController::class, 'assignUsers'])->name('shops.assign-users');
        Route::delete('/shops/{shop}/users/{user}', [ShopController::class, 'removeUser'])->name('shops.remove-user');

        // Tableau de bord spécifique par boutique
        Route::get('/shops/{shop}/dashboard', [ShopDashboardController::class, 'show'])->name('shops.dashboard');
    });

    // Routes pour la gestion des équipements des vendeurs
    Route::middleware(['auth'])->prefix('vendor-equipment')->name('vendor-equipment.')->group(function () {
        Route::get('/', [App\Http\Controllers\VendorEquipmentController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\VendorEquipmentController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\VendorEquipmentController::class, 'store'])->name('store');
        Route::get('/{equipment}', [App\Http\Controllers\VendorEquipmentController::class, 'show'])->name('show');
        Route::get('/{equipment}/edit', [App\Http\Controllers\VendorEquipmentController::class, 'edit'])->name('edit');
        Route::put('/{equipment}', [App\Http\Controllers\VendorEquipmentController::class, 'update'])->name('update');
        Route::get('/{equipment}/mark-returned', [App\Http\Controllers\VendorEquipmentController::class, 'markReturned'])->name('mark-returned');
        Route::post('/{equipment}/mark-returned', [App\Http\Controllers\VendorEquipmentController::class, 'markReturnedStore'])->name('mark-returned-store');
        Route::delete('/{equipment}', [App\Http\Controllers\VendorEquipmentController::class, 'destroy'])->name('destroy');
    });

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

    // Routes pour les campagnes
    Route::get('/campaigns', [CampaignController::class, 'index'])->name('campaigns.index');
    Route::get('/campaigns/create', [CampaignController::class, 'create'])->name('campaigns.create');
    Route::post('/campaigns', [CampaignController::class, 'store'])->name('campaigns.store');
    Route::get('/campaigns/{campaign}', [CampaignController::class, 'show'])->name('campaigns.show');
    Route::get('/campaigns/{campaign}/edit', [CampaignController::class, 'edit'])->name('campaigns.edit');
    Route::put('/campaigns/{campaign}', [CampaignController::class, 'update'])->name('campaigns.update');
    Route::delete('/campaigns/{campaign}', [CampaignController::class, 'destroy'])->name('campaigns.destroy');
    Route::get('/campaigns/{campaign}/prepare', [CampaignController::class, 'prepare'])->name('campaigns.prepare');
    Route::post('/campaigns/{campaign}/send', [CampaignController::class, 'send'])->name('campaigns.send');
    Route::post('/campaigns/{campaign}/cancel', [CampaignController::class, 'cancel'])->name('campaigns.cancel');

    // Routes pour les abonnements
    Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::get('/subscriptions/plans', [SubscriptionController::class, 'plans'])->name('subscriptions.plans');
    Route::get('/subscriptions/plans/{plan}', [SubscriptionController::class, 'create'])->name('subscriptions.create');
    Route::post('/subscriptions/plans/{plan}', [SubscriptionController::class, 'store'])->name('subscriptions.store');
    Route::get('/subscriptions/{subscription}', [SubscriptionController::class, 'show'])->name('subscriptions.show');
    Route::post('/subscriptions/{subscription}/cancel', [SubscriptionController::class, 'cancel'])->name('subscriptions.cancel');
    Route::get('/subscriptions/recharge', [SubscriptionController::class, 'rechargeForm'])->name('subscriptions.recharge.form');
    Route::post('/subscriptions/recharge', [SubscriptionController::class, 'recharge'])->name('subscriptions.recharge');

    // Routes pour les téléphones
    Route::resource('phones', PhoneController::class);

    // Routes pour la gestion des utilisateurs
    Route::get('/users', [App\Http\Controllers\UserManagementController::class, 'index'])->name('users.index');
    Route::get('/users/create', [App\Http\Controllers\UserManagementController::class, 'create'])->name('users.create');
    Route::post('/users', [App\Http\Controllers\UserManagementController::class, 'store'])->name('users.store');
    Route::get('/users/{user}', [App\Http\Controllers\UserManagementController::class, 'show'])->name('users.show');
    Route::get('/users/{user}/edit', [App\Http\Controllers\UserManagementController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [App\Http\Controllers\UserManagementController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [App\Http\Controllers\UserManagementController::class, 'destroy'])->name('users.destroy');
    Route::get('/users/{user}/reset-email', [App\Http\Controllers\UserManagementController::class, 'showResetEmailForm'])->name('users.reset-email.form');
    Route::post('/users/{user}/reset-email', [App\Http\Controllers\UserManagementController::class, 'resetEmail'])->name('users.reset-email');

    // Routes d'exportation
    Route::get('/clients/export/csv', [ClientController::class, 'export'])->name('clients.export');
    Route::get('/bills/export/csv', [BillController::class, 'export'])->name('bills.export');
    Route::get('/stats/export/csv', [DashboardController::class, 'exportStats'])->name('stats.export');

    /*
    // Routes pour les notifications - Temporairement désactivées
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
    */

    // Routes pour les commissions
    Route::middleware(['auth'])->group(function () {
        Route::get('/commissions', [CommissionController::class, 'index'])->name('commissions.index');
        Route::get('/commissions/create', [CommissionController::class, 'create'])->name('commissions.create');
        Route::post('/commissions', [CommissionController::class, 'store'])->name('commissions.store');
        Route::get('/commissions/{commission}', [CommissionController::class, 'show'])->name('commissions.show');
        Route::get('/commissions/{commission}/edit', [CommissionController::class, 'edit'])->name('commissions.edit');
        Route::put('/commissions/{commission}', [CommissionController::class, 'update'])->name('commissions.update');
        Route::delete('/commissions/{commission}', [CommissionController::class, 'destroy'])->name('commissions.destroy');
        Route::post('/commissions/{commission}/pay', [CommissionController::class, 'markAsPaid'])->name('commissions.pay');
        Route::post('/commissions/pay-batch', [CommissionController::class, 'payBatch'])->name('commissions.pay-batch');
        Route::get('/vendors/{user}/commissions', [CommissionController::class, 'vendorReport'])->name('commissions.vendor-report');
        Route::get('/users/{user}/commissions', [CommissionController::class, 'vendorReport'])->name('commissions.history');
        Route::get('/commissions/vendor/{user}/pending', [CommissionController::class, 'vendorPendingReport'])->name('commissions.pending');
        Route::get('/commissions/vendor/{user}/history', [CommissionController::class, 'vendorHistoryReport'])->name('commissions.user-history');
        Route::post('/commissions/vendor/{user}/pay', [CommissionController::class, 'payVendorCommissions'])->name('commissions.vendor-pay');
        Route::get('/shops/{shop}/commissions', [CommissionController::class, 'shopReport'])->name('commissions.shop-report');
        Route::post('/commissions/{commissionId}/pay-individual', [CommissionController::class, 'payCommission'])->name('commissions.pay-individual');
        Route::get('/commissions/export', [CommissionController::class, 'export'])->name('commissions.export');
        Route::get('/commissions/export/user/{user_id}', [CommissionController::class, 'export'])->name('commissions.export.user');

        // Routes pour les paiements de commissions
        Route::get('/commission-payments', [CommissionPaymentController::class, 'index'])->name('commission-payments.index');
        Route::get('/commission-payments/{payment}', [CommissionPaymentController::class, 'show'])->name('commission-payments.show');
        Route::get('/vendors/{user}/payments', [CommissionPaymentController::class, 'vendorHistory'])->name('commission-payments.vendor-history');
        Route::get('/shops/{shop}/payments', [CommissionPaymentController::class, 'shopHistory'])->name('commission-payments.shop-history');
    });

    // Routes pour les trocs
    Route::resource('barters', BarterController::class);

    // Nouvelles routes pour les trocs
    Route::get('/barters/{barter}/download-bill', [BarterController::class, 'downloadBill'])->name('barters.download-bill');
    Route::get('/barters/{barter}/print-bill', [BarterController::class, 'printBill'])->name('barters.print-bill');
    Route::get('/barters/{barter}/generate-bill', [BarterController::class, 'generateBill'])->name('barters.generate-bill');
    Route::get('/barter-stats', [BarterController::class, 'barterStats'])->name('barters.stats');

    // Route pour filtrer les trocs par produit
    Route::get('/barters/product/{product}', [BarterController::class, 'indexByProduct'])->name('barters.by-product');
    Route::resource('barters', App\Http\Controllers\BarterController::class);
    Route::post('barters/{barter}/complete', [App\Http\Controllers\BarterController::class, 'complete'])->name('barters.complete');
    Route::post('barters/{barter}/cancel', [App\Http\Controllers\BarterController::class, 'cancel'])->name('barters.cancel');
    Route::post('barters/{barter}/images', [App\Http\Controllers\BarterController::class, 'addImages'])->name('barters.add-images');
    Route::delete('barters/images/{image}', [App\Http\Controllers\BarterController::class, 'deleteImage'])->name('barters.delete-image');
    Route::get('api/barter/products', [App\Http\Controllers\BarterController::class, 'getBarterableProducts'])->name('api.barter.products');

    // Routes pour les livraisons
    Route::resource('deliveries', App\Http\Controllers\DeliveryController::class);
    Route::post('deliveries/{delivery}/update-status', [App\Http\Controllers\DeliveryController::class, 'updateStatus'])->name('deliveries.update-status');
    Route::post('deliveries/{delivery}/mark-delivered', [App\Http\Controllers\DeliveryController::class, 'markDelivered'])->name('deliveries.mark-delivered');
    Route::post('deliveries/{delivery}/record-payment', [App\Http\Controllers\DeliveryController::class, 'recordPayment'])->name('deliveries.record-payment');

});

// Route publique pour vérifier l'authenticité d'une facture par QR code
Route::post('verify-bill-qr', [BillController::class, 'verifyQrCode'])->name('bills.verify-qr');

// Routes pour les QR codes
Route::prefix('qrcodes')->name('qrcodes.')->group(function () {
    // Routes publiques
    Route::get('verify', [App\Http\Controllers\QrCodeController::class, 'showVerifier'])->name('verify');
    Route::post('verify-bill', [App\Http\Controllers\QrCodeController::class, 'verifyBill'])->name('verify-bill');
    Route::get('url/{type}/{id}', [App\Http\Controllers\QrCodeController::class, 'generateUrl'])->name('generate-url');

    // Routes authentifiées
    Route::middleware('auth')->group(function () {
        Route::get('generator', [App\Http\Controllers\QrCodeController::class, 'showGenerator'])->name('generator');
        Route::post('generate', [App\Http\Controllers\QrCodeController::class, 'generate'])->name('generate');
    });
});

// Routes pour l'exportation et l'importation du système (admin uniquement)
Route::prefix('admin/system')->middleware(['auth', 'can:admin'])->group(function () {
    Route::get('/export-import', [App\Http\Controllers\SystemExportImportController::class, 'index'])->name('system.export-import');
    Route::get('/export', [App\Http\Controllers\SystemExportImportController::class, 'exportSystem'])->name('system.export');
    Route::post('/import', [App\Http\Controllers\SystemExportImportController::class, 'importSystem'])->name('system.import');
    Route::post('/import/confirm', [App\Http\Controllers\SystemExportImportController::class, 'confirmImport'])->name('system.import.confirm');
    Route::get('/backup/{filename}/download', [App\Http\Controllers\SystemExportImportController::class, 'downloadBackup'])->name('system.backup.download');
    Route::delete('/backup/{filename}', [App\Http\Controllers\SystemExportImportController::class, 'deleteBackup'])->name('system.backup.delete');
});

require __DIR__.'/auth.php';

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\CashierShiftController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LanguageController;

/*
|--------------------------------------------------------------------------
| NJAMA PHARMACY - Web Routes
|--------------------------------------------------------------------------
*/

// Language Switcher
Route::get('/lang/{locale}', [LanguageController::class, 'switchLang'])->name('lang.switch');

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Authenticated Application Routes
Route::middleware(['auth'])->group(function () {
    // Root & Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // User Profile
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::post('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');

    // POS (Cashier, Pharmacist, Admin)
    Route::middleware(['role:admin,pharmacist,cashier'])->group(function () {
        Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
        Route::get('/pos/search', [PosController::class, 'search'])->name('pos.search');
        Route::post('/pos/checkout', [PosController::class, 'checkout'])->name('pos.checkout');
        Route::get('/pos/receipt/{sale}', [PosController::class, 'receipt'])->name('pos.receipt');
        Route::get('/pos/digital-receipt/{sale}', [PosController::class, 'digitalReceipt'])->name('pos.digital-receipt');
        Route::post('/pos/sync', [PosController::class, 'syncOfflineSales'])->name('pos.sync');

        // Cashier Shifts
        Route::get('/shifts', [CashierShiftController::class, 'index'])->name('shifts.index');
        Route::post('/shifts/open', [CashierShiftController::class, 'open'])->name('shifts.open');
        Route::post('/shifts/{shift}/close', [CashierShiftController::class, 'close'])->name('shifts.close');
    });

    // Medicines Catalogue & Inventory
    Route::middleware(['role:admin,pharmacist,inventory_officer,auditor'])->group(function () {
        Route::get('/products', [ProductController::class, 'index'])->name('products.index');
        Route::get('/products/show/{product}', [ProductController::class, 'show'])->name('products.show');
        Route::get('/inventory/movements', [BatchController::class, 'movements'])->name('batches.movements');
        Route::get('/batches', [BatchController::class, 'index'])->name('batches.index');
    });

    Route::middleware(['role:admin,pharmacist,inventory_officer'])->group(function () {
        Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');

        // Bulk Price Updater
        Route::get('/products-bulk-prices', [ProductController::class, 'bulkPriceUpdater'])->name('products.bulk-prices');
        Route::post('/products-bulk-prices', [ProductController::class, 'saveBulkPrices'])->name('products.save-bulk-prices');

        // Batch Actions: Quarantine, Write-off
        Route::post('/batches/{batch}/quarantine', [BatchController::class, 'quarantine'])->name('batches.quarantine');
        Route::post('/batches/{batch}/write-off', [BatchController::class, 'writeOff'])->name('batches.write-off');
    });

    // Purchases & Suppliers
    Route::middleware(['role:admin,inventory_officer,pharmacist,auditor'])->group(function () {
        Route::get('/purchases', [PurchaseController::class, 'index'])->name('purchases.index');
        Route::get('/purchases/{purchase}', [PurchaseController::class, 'show'])->name('purchases.show');
        Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
        Route::get('/suppliers/{supplier}', [SupplierController::class, 'show'])->name('suppliers.show');
    });

    Route::middleware(['role:admin,inventory_officer'])->group(function () {
        Route::get('/purchase/new', [PurchaseController::class, 'create'])->name('purchases.create');
        Route::post('/purchases', [PurchaseController::class, 'store'])->name('purchases.store');
        Route::post('/purchases/{purchase}/payment', [PurchaseController::class, 'recordPayment'])->name('purchases.payment');
        Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
        Route::put('/suppliers/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
    });

    // Prescriptions & Dispensing Records
    Route::middleware(['role:admin,pharmacist'])->group(function () {
        Route::get('/prescriptions', [PrescriptionController::class, 'index'])->name('prescriptions.index');
        Route::get('/prescriptions/create', [PrescriptionController::class, 'create'])->name('prescriptions.create');
        Route::post('/prescriptions', [PrescriptionController::class, 'store'])->name('prescriptions.store');
        Route::get('/prescriptions/{prescription}', [PrescriptionController::class, 'show'])->name('prescriptions.show');
        Route::get('/dispensing-records', [PrescriptionController::class, 'dispensingRecords'])->name('prescriptions.dispensing');
    });

    // Operating Expenses
    Route::middleware(['role:admin,cashier,auditor'])->group(function () {
        Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
        Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
    });

    // Financial Reports & Analytics (Admin & Auditor)
    Route::middleware(['role:admin,auditor'])->group(function () {
        Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
        Route::get('/reports/profit-loss', [ReportController::class, 'profitLoss'])->name('reports.profit-loss');
        Route::get('/reports/inventory-valuation', [ReportController::class, 'inventoryValuation'])->name('reports.inventory-valuation');
        Route::get('/reports/expiry', [ReportController::class, 'expiryReport'])->name('reports.expiry');
    });

    // Administration, Staff, Settings, Audit Logs, and Backups (Admin Only)
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');

        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');

        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit.index');

        Route::get('/backups', [BackupController::class, 'index'])->name('backups.index');
        Route::post('/backups/create', [BackupController::class, 'createBackup'])->name('backups.create');
        Route::get('/backups/download/{backup}', [BackupController::class, 'download'])->name('backups.download');
    });
});

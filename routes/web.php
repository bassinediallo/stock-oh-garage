<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StockReportController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\DashboardController;
use App\Models\StockMovement;
use App\Models\Site;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

// Routes d'authentification
Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Routes protégées par authentification
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Sites
    Route::resource('sites', SiteController::class);

    // Departments
    Route::resource('departments', DepartmentController::class);

    // Products
    Route::resource('products', ProductController::class);

    // Stock Movements
    Route::get('stock-movements/export', [StockMovementController::class, 'export'])->name('stock-movements.export');
    Route::resource('stock-movements', StockMovementController::class)->except(['edit', 'update', 'destroy']);
    Route::get('stock-movements/report', [StockMovementController::class, 'report'])->name('stock-movements.report');

    // Suppliers
    Route::resource('suppliers', SupplierController::class);
    Route::get('/api/suppliers/search', [SupplierController::class, 'search'])->name('suppliers.search');
    Route::post('/api/suppliers/quick-store', [SupplierController::class, 'quickStore'])->name('suppliers.quick-store');

    // Users
    Route::resource('users', UserController::class);

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
    Route::get('/reports/stock', [StockReportController::class, 'index'])->name('reports.stock');
    Route::get('/reports/stock/export', [StockReportController::class, 'export'])->name('reports.stock.export');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/preferences', [ProfileController::class, 'updatePreferences'])->name('profile.preferences');
});

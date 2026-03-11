<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TokoController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\TopUpController;
use App\Http\Controllers\AdminTokoController;
use App\Http\Controllers\PenjualController;
use App\Http\Controllers\SaldoHistoryController;
use App\Http\Controllers\WithdrawalController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // User Routes
    Route::middleware('role:user')->group(function () {
        Route::get('/menus', [MenuController::class, 'index'])->name('menus.index');
        Route::get('/menus/toko/{toko}', [MenuController::class, 'byToko'])->name('menus.toko');
        Route::get('/menus/{menu}', [MenuController::class, 'show'])->name('menus.show');

        Route::get('/tokos', [TokoController::class, 'index'])->name('tokos.index');
        Route::get('/tokos/{toko}', [TokoController::class, 'show'])->name('tokos.show');

        Route::get('/cart', function () {
            return view('cart.index');
        })->name('cart.index');

        Route::get('/checkout', [OrderController::class, 'showCheckout'])->name('checkout.show');
        Route::post('/checkout', [OrderController::class, 'prepareCheckout'])->name('checkout.prepare');
        Route::post('/orders/place', [OrderController::class, 'placeOrder'])->name('orders.place');
        Route::get('/orders/{order}/payment', [OrderController::class, 'showPayment'])->name('orders.payment');
        Route::post('/orders/{order}/confirm-payment', [OrderController::class, 'confirmPayment'])->name('orders.confirm-payment');

        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/riwayat', [OrderController::class, 'riwayat'])->name('orders.riwayat');
        Route::get('/orders/siap-count', [OrderController::class, 'siapCount'])->name('orders.siap-count');
        Route::post('/orders/checkout', [OrderController::class, 'checkout'])->name('orders.checkout');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');

        Route::get('/topup', [TopUpController::class, 'index'])->name('topup.index');
        Route::post('/topup/generate', [TopUpController::class, 'generateCode'])->name('topup.generate');
        Route::get('/topup/history', [TopUpController::class, 'history'])->name('topup.history');
        Route::get('/topup/check-approval', [TopUpController::class, 'checkApproval'])->name('topup.check-approval');

        Route::get('/saldo', [SaldoHistoryController::class, 'index'])->name('saldo.index');
    });

    // Admin Routes
    Route::middleware('role:admin')->prefix('admin-toko')->name('admin-toko.')->group(function () {
        Route::get('/dashboard', [AdminTokoController::class, 'dashboard'])->name('dashboard');
        Route::post('/orders/{order}/update-status', [AdminTokoController::class, 'updateOrderStatus'])->name('orders.update-status');

        // Toko CRUD
        Route::get('/tokos', [AdminTokoController::class, 'tokos'])->name('tokos');
        Route::get('/tokos/create', [AdminTokoController::class, 'createToko'])->name('tokos.create');
        Route::post('/tokos', [AdminTokoController::class, 'storeToko'])->name('tokos.store');
        Route::get('/tokos/{toko}/edit', [AdminTokoController::class, 'editToko'])->name('tokos.edit');
        Route::put('/tokos/{toko}', [AdminTokoController::class, 'updateToko'])->name('tokos.update');
        Route::delete('/tokos/{toko}', [AdminTokoController::class, 'destroyToko'])->name('tokos.destroy');

        // Menu CRUD (scoped per toko)
        Route::get('/tokos/{toko}/menus', [AdminTokoController::class, 'menus'])->name('tokos.menus');
        Route::get('/tokos/{toko}/menus/create', [AdminTokoController::class, 'createMenu'])->name('tokos.menus.create');
        Route::post('/tokos/{toko}/menus', [AdminTokoController::class, 'storeMenu'])->name('tokos.menus.store');
        Route::get('/tokos/{toko}/menus/{menu}/edit', [AdminTokoController::class, 'editMenu'])->name('tokos.menus.edit');
        Route::put('/tokos/{toko}/menus/{menu}', [AdminTokoController::class, 'updateMenu'])->name('tokos.menus.update');
        Route::delete('/tokos/{toko}/menus/{menu}', [AdminTokoController::class, 'destroyMenu'])->name('tokos.menus.destroy');

        Route::get('/validate-topup', [TopUpController::class, 'showValidationForm'])->name('validate-topup');
        Route::post('/validate-topup', [TopUpController::class, 'validateCode'])->name('validate-topup.process');

        Route::get('/arsip', [AdminTokoController::class, 'arsip'])->name('arsip');
        Route::get('/arsip/{toko}', [AdminTokoController::class, 'arsipToko'])->name('arsip.toko');

        // Pencairan Saldo
        Route::get('/pencairan', [WithdrawalController::class, 'adminIndex'])->name('pencairan');
        Route::post('/pencairan/{withdrawal}/approve', [WithdrawalController::class, 'approve'])->name('pencairan.approve');
        Route::post('/pencairan/{withdrawal}/reject', [WithdrawalController::class, 'reject'])->name('pencairan.reject');

        // Antri Pesanan
        Route::get('/antri', [AdminTokoController::class, 'antri'])->name('antri');
        Route::post('/antri/{order}/update-status', [AdminTokoController::class, 'updateOrderStatus'])->name('antri.update-status');

        // Kelola User
        Route::get('/users', [AdminTokoController::class, 'users'])->name('users');
        Route::get('/users/create', [AdminTokoController::class, 'createUser'])->name('users.create');
        Route::post('/users', [AdminTokoController::class, 'storeUser'])->name('users.store');
        Route::get('/users/{user}/edit', [AdminTokoController::class, 'editUser'])->name('users.edit');
        Route::put('/users/{user}', [AdminTokoController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{user}', [AdminTokoController::class, 'destroyUser'])->name('users.destroy');
    });

    // Penjual (Toko Role) Routes
    Route::middleware('role:toko')->prefix('penjual')->name('penjual.')->group(function () {
        // First-time toko setup
        Route::get('/setup', [PenjualController::class, 'setup'])->name('setup');
        Route::post('/setup', [PenjualController::class, 'storeSetup'])->name('setup.store');

        // Dashboard
        Route::get('/dashboard', [PenjualController::class, 'dashboard'])->name('dashboard');

        // Order queue (dedicated antri page)
        Route::get('/antri', [PenjualController::class, 'antri'])->name('antri');
        Route::get('/rekap', [PenjualController::class, 'rekap'])->name('rekap');

        // Order management
        Route::post('/orders/{order}/update-status', [PenjualController::class, 'updateOrderStatus'])->name('orders.update-status');

        // Pencairan Saldo
        Route::get('/pencairan', [WithdrawalController::class, 'index'])->name('pencairan');
        Route::post('/pencairan', [WithdrawalController::class, 'store'])->name('pencairan.store');

        // Menu management
        Route::get('/menus', [PenjualController::class, 'menus'])->name('menus');
        Route::get('/menus/create', [PenjualController::class, 'createMenu'])->name('menus.create');
        Route::post('/menus', [PenjualController::class, 'storeMenu'])->name('menus.store');
        Route::get('/menus/{menu}/edit', [PenjualController::class, 'editMenu'])->name('menus.edit');
        Route::put('/menus/{menu}', [PenjualController::class, 'updateMenu'])->name('menus.update');
        Route::post('/menus/{menu}/toggle-status', [PenjualController::class, 'toggleMenuStatus'])->name('menus.toggle-status');
        Route::delete('/menus/{menu}', [PenjualController::class, 'destroyMenu'])->name('menus.destroy');
    });
});

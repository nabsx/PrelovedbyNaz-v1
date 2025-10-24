<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [ProductController::class, 'index'])->name('home');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

// Authentication Routes Manual (ganti Auth::routes())
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

// Cart routes (available for guests and users)
Route::prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('cart.index');
    Route::post('/add', [CartController::class, 'store'])->name('cart.store');
    Route::put('/{cartItem}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/{cartItem}', [CartController::class, 'destroy'])->name('cart.destroy');
    Route::post('/clear', [CartController::class, 'clear'])->name('cart.clear');
});

// Authenticated user routes
Route::middleware('auth')->group(function () {
    Route::post('/checkout', [TransactionController::class, 'checkout'])->name('checkout');
    Route::get('/transactions/history', [TransactionController::class, 'history'])->name('transactions.history');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('products', AdminProductController::class);
    Route::get('/dashboard', function () {
        $products = \App\Models\Product::count();
        $transactions = \App\Models\Transaction::count();
        $revenue = \App\Models\Transaction::where('status', 'paid')->sum('total_price');
        
        return view('admin.dashboard', compact('products', 'transactions', 'revenue'));
    })->name('dashboard');
});

// Midtrans notification handler
Route::post('/payment/notification', [TransactionController::class, 'handleNotification'])
    ->name('payment.notification');
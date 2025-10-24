<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [ProductController::class, 'index'])->name('home');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Cart routes (available for guests and users)
Route::prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('cart.index');
    Route::post('/add', [CartController::class, 'store'])->name('cart.store');
    Route::put('/{cartItem}', [CartController::class, 'update'])->name('cart.update');
    Route::post('/{cartItem}/update-quantity', [CartController::class, 'updateQuantityAjax'])->name('cart.updateQuantityAjax');
    Route::delete('/{cartItem}', [CartController::class, 'destroy'])->name('cart.destroy');
    Route::post('/clear', [CartController::class, 'clear'])->name('cart.clear');
});

// Authenticated user routes
Route::middleware('auth')->group(function () {
    Route::get('/checkout', [TransactionController::class, 'showCheckoutForm'])->name('checkout.form');
    Route::post('/checkout', [TransactionController::class, 'checkout'])->name('checkout');
    Route::get('/checkout/success/{transaction_code}', [TransactionController::class, 'success'])->name('checkout.success');
    Route::get('/transactions/history', [TransactionController::class, 'history'])->name('transactions.history');
});

// Admin routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access. Admin only.');
        }
        
        $products = \App\Models\Product::count();
        $transactions = \App\Models\Transaction::count();
        $revenue = \App\Models\Transaction::where('status', 'paid')->sum('total_price');
        $users = \App\Models\User::count();
        $recentProducts = \App\Models\Product::with('category')->latest()->take(5)->get();
        $lowStockProducts = \App\Models\Product::where('stock', '<', 5)->where('stock', '>', 0)->get();
        
        return view('admin.dashboard', compact(
            'products', 
            'transactions', 
            'revenue', 
            'users',
            'recentProducts',
            'lowStockProducts'
        ));
    })->name('dashboard');

    // Products routes
    Route::resource('products', AdminProductController::class);
    
    // Categories routes
    Route::resource('categories', CategoryController::class);
    
    // Users routes - PERBAIKI INI: tambahkan namespace Admin
    Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'show'])->name('users.show');
    Route::put('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
});


// Midtrans notification handler
Route::post('/payment/notification', [TransactionController::class, 'handleNotification'])
    ->name('payment.notification');

// Password reset routes (letakkan di luar group auth)
Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminProductController;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Route;

// Home & info pages
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/privacy', [HomeController::class, 'privacy'])->name('privacy');
Route::get('/shipping', [HomeController::class, 'shipping'])->name('shipping');
Route::get('/faq', [HomeController::class, 'faq'])->name('faq');

// Products
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/search', [ProductController::class, 'search'])->name('products.search');

// Cart (session-based, JSON API)
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/',          [CartController::class, 'index'])->name('index');
    Route::post('/add',      [CartController::class, 'add'])->name('add');
    Route::patch('/update',  [CartController::class, 'update'])->name('update');
    Route::delete('/remove', [CartController::class, 'remove'])->name('remove');
    Route::delete('/clear',  [CartController::class, 'clear'])->name('clear');
});

// Checkout
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

// Public JSON API (for the static frontend)
Route::prefix('api')->name('api.')->group(function () {
    Route::options('/{any}',  [ApiController::class, 'handleOptions'])->where('any', '.*');
    Route::get('/products',   [ApiController::class, 'products'])->name('products');
    Route::post('/orders',    [ApiController::class, 'storeOrder'])->name('orders');
    Route::post('/contact',   [ApiController::class, 'storeTicket'])->name('contact');
    Route::post('/analytics', [ApiController::class, 'storeAnalytics'])->name('analytics');
});

// Admin
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login',  [AdminController::class, 'loginForm'])->name('login');
    Route::post('/login', [AdminController::class, 'login'])->name('login.post');
    Route::post('/logout', [AdminController::class, 'logout'])->name('logout');

    Route::middleware('admin')->group(function () {
        Route::get('/',                              [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/orders',                        [AdminController::class, 'orders'])->name('orders');
        Route::get('/orders/{order}',                [AdminController::class, 'orderShow'])->name('orders.show');
        Route::post('/orders/{order}/status',        [AdminController::class, 'orderUpdateStatus'])->name('orders.status');
        Route::delete('/orders/{order}',             [AdminController::class, 'orderDelete'])->name('orders.delete');

        // Profile
        Route::get('/profile',                       [AdminController::class, 'profileForm'])->name('profile');
        Route::post('/profile/password',             [AdminController::class, 'changePassword'])->name('profile.password');
        Route::post('/profile/name',                 [AdminController::class, 'changeName'])->name('profile.name');

        // Analytics
        Route::get('/analytics',      [AdminController::class, 'analytics'])->name('analytics');
        Route::get('/orders/latest-id',[AdminController::class, 'latestOrderId'])->name('orders.latest-id');

        // Support Tickets
        Route::get('/tickets',                       [AdminController::class, 'tickets'])->name('tickets');
        Route::get('/tickets/{ticket}',              [AdminController::class, 'ticketShow'])->name('tickets.show');
        Route::post('/tickets/{ticket}/status',      [AdminController::class, 'ticketUpdateStatus'])->name('tickets.status');
        Route::delete('/tickets/{ticket}',           [AdminController::class, 'ticketDelete'])->name('tickets.delete');

        // Products
        Route::get('/products',                      [AdminProductController::class, 'index'])->name('products.index');
        Route::get('/products/create',               [AdminProductController::class, 'create'])->name('products.create');
        Route::post('/products',                     [AdminProductController::class, 'store'])->name('products.store');
        Route::get('/products/{product}/edit',       [AdminProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{product}',            [AdminProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}',         [AdminProductController::class, 'destroy'])->name('products.destroy');
        Route::post('/products/{product}/toggle',    [AdminProductController::class, 'toggle'])->name('products.toggle');
    });
});

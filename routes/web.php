<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

Auth::routes();

// Routes for admin with read-only access to products and categories
Route::group(['middleware' => ['role:admin']], function () {
    // Order routes
    Route::resource('orders', OrderController::class)->only(['index', 'show']);
});


// Routes for admin with read-only access to products and categories
Route::group(['middleware' => ['role:superadmin|admin']], function () {
     // Home route
     Route::get('/admin', [HomeController::class, 'index'])->name('home');
});

// Routes for superadmin and cashier
Route::group(['middleware' => ['role:superadmin|cashier']], function () {
    // Cart routes
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
    Route::post('/cart/change-qty', [CartController::class, 'changeQty']);
    Route::post('/cart/capital', [CartController::class, 'modal'])->name('cart.capital');
    Route::delete('/cart/delete', [CartController::class, 'delete']);
    Route::delete('/cart/empty', [CartController::class, 'empty']);
});

// Routes for superadmin and cashier
Route::group(['middleware' => ['role:superadmin|admin|cashier']], function () {
    // Order routes
    Route::resource('orders', OrderController::class)->only(['index', 'show', 'create', 'store', 'edit', 'update']);
    Route::get('/orders/{id}/details', [OrderController::class, 'details'])->name('orders.details');
    Route::get('/orders/list', [OrderController::class, 'getOrderList'])->name('orders.list');
    Route::post('/orders/{id}/upload-proof', [OrderController::class, 'uploadProof'])->name('orders.uploadProof');
    Route::get('/orders/{order}/print', [OrderController::class, 'print'])->name('orders.print');
});

// Routes for superadmin and inventory
Route::group(['middleware' => ['role:superadmin|inventory']], function () {
    // Category routes
    Route::resource('categories', CategoryController::class);

    // Product routes
    Route::get('/inventory', [ProductController::class, 'index'])->name('inventory.index');
    Route::resource('products', ProductController::class);
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    Route::post('/products/import', [ProductController::class, 'import'])->name('products.import');

   
});

// Routes for superadmin, admin, cashier, and inventory
Route::group(['middleware' => ['role:superadmin|admin|inventory']], function () {
    // Category routes
    Route::resource('categories', CategoryController::class)->only(['index', 'show']);
});

// Routes for superadmin, cashier, and inventory
Route::group(['middleware' => ['role:superadmin|admin|cashier|inventory']], function () {
    // Product routes
    Route::resource('products', ProductController::class)->except(['create', 'store', 'edit', 'update','destroy']);

    // Customer routes
    Route::resource('customers', CustomerController::class);
});

// Routes for superadmin only
Route::group(['middleware' => ['role:superadmin']], function () {
    // User routes
    Route::resource('users', UserController::class);

    // Settings routes
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'store'])->name('settings.store');

    Route::resource('orders', OrderController::class)->only(['destroy']);
    
    });


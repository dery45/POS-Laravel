<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return redirect('/admin');
});

Auth::routes();

Route::group(['middleware' => ['role:superadmin|admin|cashier', 'token.expired']], function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
    Route::post('/cart/change-qty', [CartController::class, 'changeQty']);
    Route::post('/cart/capital',[CartController::class, 'modal'])->name('cart.capital');
    Route::delete('/cart/delete', [CartController::class, 'delete']);
    Route::delete('/cart/empty', [CartController::class, 'empty']);
    Route::resource('orders', OrderController::class)->only(['index', 'show', 'create', 'store']);
});

Route::group(['middleware' => ['role:superadmin|admin|inventory', 'token.expired']], function () {
    Route::resource('categories', CategoryController::class);
    Route::get('/inventory', [ProductController::class, 'index'])->name('inventory.index');
    Route::resource('products', ProductController::class);
    Route::post('/products/import', [ProductController::class, 'import'])->name('products.import');
    Route::get('/search', 'SearchController@search')->name('search');
});

<<<<<<< HEAD
Route::group(['middleware' => ['role:superadmin|admin|cashier|inventory']], function () {
=======
Route::group(['middleware' => ['role:superadmin|admin|cashier|inventory']], function () {
    Route::get('/autocomplete/search', 'AutocompleteController@search')->name('autocomplete.search');
>>>>>>> eddb490257db3c9c9a00805bde2182124072feaa
    Route::get('/admin', [HomeController::class, 'index'])->name('home');
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'store'])->name('settings.store');
    Route::resource('customers', CustomerController::class);
});

Route::group(['middleware' => ['role:superadmin', 'token.expired']], function () {
    Route::resource('users', UserController::class);
});
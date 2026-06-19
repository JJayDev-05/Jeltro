<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\AdminOrderController;
use App\Http\Controllers\AdminProductController;
use App\Http\Controllers\SavedProductController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use Illuminate\Support\Facades\Route;

// Static pages
Route::view('/about', 'pages.about')->name('about');
Route::view('/journal', 'pages.journal')->name('journal');

// Search page
Route::get('/search', [ShopController::class, 'search'])->name('search');
Route::get('/search/suggestions', [ShopController::class, 'suggestions'])->name('search.suggestions');

// Shop
Route::get('/', [ShopController::class, 'home'])->name('home');
Route::get('/shop', [ShopController::class, 'index'])->name('shop');
Route::get('/shop/{slug}', [ShopController::class, 'show'])->name('shop.show');

// Auth required
Route::middleware('auth')->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/{id}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{id}', [CartController::class, 'remove'])->name('cart.remove');

    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');

    Route::get('/notifications/read', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return back();
    })->name('notifications.read');

    Route::delete('/notifications/{id}', function ($id) {
        auth()->user()->notifications()->where('id', $id)->delete();
        return response()->json(['ok' => true]);
    })->name('notifications.delete');

    Route::get('/account', [AccountController::class, 'index'])->name('account');
    Route::get('/account/orders/{order}', [AccountController::class, 'order'])->name('account.order');
    Route::post('/account/orders/{order}/cancel-request', [AccountController::class, 'cancelRequest'])->name('account.order.cancel-request');
    Route::patch('/account', [AccountController::class, 'update'])->name('account.update');
    Route::post('/account/avatar', [AccountController::class, 'uploadAvatar'])->name('account.avatar');
    Route::post('/saved/{product}/toggle', [SavedProductController::class, 'toggle'])->name('saved.toggle');
    Route::post('/account/addresses', [AddressController::class, 'store'])->name('address.store');
    Route::patch('/account/addresses/{address}/default', [AddressController::class, 'setDefault'])->name('address.default');
    Route::delete('/account/addresses/{address}', [AddressController::class, 'destroy'])->name('address.destroy');
});

// Admin (auth + admin required)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', fn() => redirect()->route('admin.products.index'));
    Route::get('/products/archived', [AdminProductController::class, 'archived'])->name('products.archived');
    Route::post('/products/{product}/archive', [AdminProductController::class, 'archive'])->name('products.archive');
    Route::post('/products/{id}/restore', [AdminProductController::class, 'restore'])->name('products.restore');
    Route::post('/products/{id}/force-delete', [AdminProductController::class, 'forceDelete'])->name('products.force-delete');
    Route::resource('products', AdminProductController::class)->except('destroy');
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');
    Route::patch('/orders/{order}/cancel-approve', [AdminOrderController::class, 'approveCancel'])->name('orders.cancel-approve');
    Route::patch('/orders/{order}/cancel-reject', [AdminOrderController::class, 'rejectCancel'])->name('orders.cancel-reject');
});

require __DIR__.'/auth.php';

<?php

use App\Http\Controllers\Api\ProductApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Read-only product endpoints consumed by the AI shopping assistant. Every
| route is protected by the "verify.api.key" middleware, so callers must send
| a valid X-Api-Key header.
|
*/

Route::middleware('verify.api.key')->group(function () {
    Route::get('/products', [ProductApiController::class, 'search'])->name('api.products.search');
    Route::get('/products/{slug}', [ProductApiController::class, 'show'])->name('api.products.show');
});

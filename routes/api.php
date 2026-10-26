<?php

use App\Http\Controllers\Currencies\CurrenciesGetController;
use App\Http\Controllers\Products\ProductDeleteController;
use App\Http\Controllers\Products\ProductGetController;
use App\Http\Controllers\Products\ProductPostController;
use App\Http\Controllers\Products\ProductsGetController;
use Illuminate\Support\Facades\Route;

Route::get('/status', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'API is running',
    ]);
});

Route::get('/currencies', CurrenciesGetController::class);
Route::get('/products', ProductsGetController::class);
Route::get('/products/{id}', ProductGetController::class)->whereNumber('id');
Route::post('/products', ProductPostController::class);
Route::delete('/products/{id}', ProductDeleteController::class)->whereNumber('id');

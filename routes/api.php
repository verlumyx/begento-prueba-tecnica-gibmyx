<?php

use App\Http\Controllers\Currencies\CurrenciesGetController;
use App\Http\Controllers\Currencies\CurrencyDeleteController;
use App\Http\Controllers\Currencies\CurrencyGetController;
use App\Http\Controllers\Currencies\CurrencyPostController;
use App\Http\Controllers\Currencies\CurrencyPutController;
use App\Http\Controllers\Products\ProductDeleteController;
use App\Http\Controllers\Products\ProductGetController;
use App\Http\Controllers\Products\ProductPostController;
use App\Http\Controllers\Products\ProductPutController;
use App\Http\Controllers\Products\ProductsGetController;
use Illuminate\Support\Facades\Route;

Route::get('/status', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'API is running',
    ]);
});

Route::get('/currencies', CurrenciesGetController::class);
Route::post('/currencies', CurrencyPostController::class);
Route::get('/currencies/{id}', CurrencyGetController::class)->whereNumber('id');
Route::put('/currencies/{id}', CurrencyPutController::class)->whereNumber('id');
Route::delete('/currencies/{id}', CurrencyDeleteController::class)->whereNumber('id');

Route::get('/products', ProductsGetController::class);
Route::get('/products/{id}', ProductGetController::class)->whereNumber('id');
Route::post('/products', ProductPostController::class);
Route::put('/products/{id}', ProductPutController::class)->whereNumber('id');
Route::delete('/products/{id}', ProductDeleteController::class)->whereNumber('id');

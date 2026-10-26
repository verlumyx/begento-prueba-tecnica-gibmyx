<?php

use App\Http\Controllers\Currencies\CurrenciesGetController;
use App\Http\Controllers\Products\ProductPostController;
use Illuminate\Support\Facades\Route;

Route::get('/status', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'API is running',
    ]);
});

Route::get('/currencies', CurrenciesGetController::class);
Route::post('/products', ProductPostController::class);

<?php

use App\Http\Controllers\Prices\CurrenciesGetController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/status', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'API is running',
    ]);
});

Route::get('/currencies', CurrenciesGetController::class);

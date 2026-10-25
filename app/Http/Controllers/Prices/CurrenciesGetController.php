<?php

namespace App\Http\Controllers\Prices;

use App\Http\Controllers\Controller;
use App\Services\CurrenciesServices;
use Illuminate\Http\JsonResponse;

class CurrenciesGetController extends Controller
{
    public function __construct()
    {
    }

    public function __invoke(CurrenciesServices $currenciesServices): JsonResponse
    {
        return response()->json($currenciesServices->all());
    }
}

<?php

namespace App\Http\Controllers\Currencies;

use App\Http\Controllers\Controller;
use App\Http\Requests\Currencies\StoreCurrencyRequest;
use App\Services\CurrenciesServices;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class CurrencyPostController extends Controller
{
    public function __construct() {}

    #[OA\Post(
        path: '/currencies',
        summary: 'Crear una nueva divisa',
        description: 'Registra una nueva divisa en el sistema con su nombre, símbolo y tasa de cambio.',
        tags: ['Currencies'],
        requestBody: new OA\RequestBody(
            required: true,
            description: 'Datos requeridos para la creación de una divisa',
            content: new OA\JsonContent(
                required: ['name', 'symbol', 'exchange_rate'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'US Dollar'),
                    new OA\Property(property: 'symbol', type: 'string', example: '$'),
                    new OA\Property(property: 'exchange_rate', type: 'number', format: 'float', example: 1.0000),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Divisa creada exitosamente',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 1),
                        new OA\Property(property: 'name', type: 'string', example: 'US Dollar'),
                        new OA\Property(property: 'symbol', type: 'string', example: '$'),
                        new OA\Property(property: 'exchange_rate', type: 'string', example: '1.0000'),
                        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2026-10-25T21:42:30.000000Z'),
                        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2026-10-25T21:42:30.000000Z'),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Error de validación en los datos enviados'
            ),
            new OA\Response(
                response: 500,
                description: 'Error interno del servidor'
            ),
        ]
    )]
    public function __invoke(StoreCurrencyRequest $request, CurrenciesServices $currenciesServices): JsonResponse
    {
        $currency = $currenciesServices->create($request->validated());

        return response()->json($currency, 201);
    }
}

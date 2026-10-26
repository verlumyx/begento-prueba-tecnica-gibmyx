<?php

namespace App\Http\Controllers\Currencies;

use App\Http\Controllers\Controller;
use App\Http\Requests\Currencies\UpdateCurrencyRequest;
use App\Services\CurrenciesServices;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class CurrencyPutController extends Controller
{
    public function __construct() {}

    #[OA\Put(
        path: '/currencies/{id}',
        summary: 'Actualizar una divisa',
        description: 'Actualiza los datos de una divisa existente mediante su ID.',
        tags: ['Currencies'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                description: 'Identificador único de la divisa',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            description: 'Datos a actualizar de la divisa',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'US Dollar Updated'),
                    new OA\Property(property: 'symbol', type: 'string', example: 'USD$'),
                    new OA\Property(property: 'exchange_rate', type: 'number', format: 'float', example: 1.0500),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Divisa actualizada exitosamente',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 1),
                        new OA\Property(property: 'name', type: 'string', example: 'US Dollar Updated'),
                        new OA\Property(property: 'symbol', type: 'string', example: 'USD$'),
                        new OA\Property(property: 'exchange_rate', type: 'string', example: '1.0500'),
                        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2026-10-25T21:42:30.000000Z'),
                        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2026-10-25T21:42:30.000000Z'),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Divisa no encontrada',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'La divisa solicitada no fue encontrada.'),
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
    public function __invoke(int $id, UpdateCurrencyRequest $request, CurrenciesServices $currenciesServices): JsonResponse
    {
        $currency = $currenciesServices->update($id, $request->validated());

        return response()->json($currency);
    }
}

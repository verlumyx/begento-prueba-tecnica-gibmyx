<?php

namespace App\Http\Controllers\Currencies;

use App\Http\Controllers\Controller;
use App\Services\CurrenciesServices;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class CurrencyGetController extends Controller
{
    public function __construct() {}

    #[OA\Get(
        path: '/currencies/{id}',
        summary: 'Obtener una divisa por ID',
        description: 'Retorna los datos de una divisa específica mediante su ID.',
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
        responses: [
            new OA\Response(
                response: 200,
                description: 'Divisa obtenida exitosamente',
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
                response: 404,
                description: 'Divisa no encontrada',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'La divisa solicitada no fue encontrada.'),
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Error interno del servidor'
            ),
        ]
    )]
    public function __invoke(int $id, CurrenciesServices $currenciesServices): JsonResponse
    {
        $currency = $currenciesServices->findById($id);

        return response()->json($currency);
    }
}

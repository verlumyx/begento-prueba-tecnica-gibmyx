<?php

namespace App\Http\Controllers\Currencies;

use App\Http\Controllers\Controller;
use App\Services\CurrenciesServices;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class CurrenciesGetController extends Controller
{
    public function __construct() {}

    #[OA\Get(
        path: '/currencies',
        summary: 'Listar todas las divisas',
        description: 'Retorna el listado completo de divisas disponibles en el sistema con sus tasas de cambio.',
        tags: ['Currencies'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lista de divisas obtenida exitosamente',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'id', type: 'integer', example: 1),
                            new OA\Property(property: 'name', type: 'string', example: 'US Dollar'),
                            new OA\Property(property: 'symbol', type: 'string', example: '$'),
                            new OA\Property(property: 'exchange_rate', type: 'string', example: '1.0000'),
                            new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2026-10-25T21:42:30.000000Z'),
                            new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2026-10-25T21:42:30.000000Z'),
                        ]
                    )
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Error interno del servidor'
            ),
        ]
    )]
    public function __invoke(CurrenciesServices $currenciesServices): JsonResponse
    {
        return response()->json($currenciesServices->all());
    }
}

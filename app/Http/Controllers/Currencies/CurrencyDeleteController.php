<?php

namespace App\Http\Controllers\Currencies;

use App\Http\Controllers\Controller;
use App\Services\CurrenciesServices;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class CurrencyDeleteController extends Controller
{
    public function __construct() {}

    #[OA\Delete(
        path: '/currencies/{id}',
        summary: 'Eliminar una divisa por ID',
        description: 'Elimina una divisa y los registros asociados mediante su ID.',
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
                description: 'Divisa eliminada exitosamente',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Divisa eliminada exitosamente.'),
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
        $currenciesServices->delete($id);

        return response()->json([
            'message' => 'Divisa eliminada exitosamente.',
        ]);
    }
}

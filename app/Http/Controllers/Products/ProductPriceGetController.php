<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Services\ProductPricesServices;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class ProductPriceGetController extends Controller
{
    public function __construct() {}

    #[OA\Get(
        path: '/products/{id}/prices',
        summary: 'Obtener lista de precios de un producto',
        description: 'Retorna la lista de precios registrados para un producto específico en diferentes divisas.',
        tags: ['Products'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                description: 'Identificador único del producto',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lista de precios obtenida exitosamente',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'id', type: 'integer', example: 1),
                            new OA\Property(property: 'product_id', type: 'integer', example: 1),
                            new OA\Property(property: 'currency_id', type: 'integer', example: 2),
                            new OA\Property(property: 'price', type: 'string', example: '1104.46'),
                            new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2026-10-25T21:42:30.000000Z'),
                            new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2026-10-25T21:42:30.000000Z'),
                        ]
                    )
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Producto no encontrado',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'El producto solicitado no fue encontrado.'),
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Error interno del servidor'
            ),
        ]
    )]
    public function __invoke(int $id, ProductPricesServices $productPricesServices): JsonResponse
    {
        $prices = $productPricesServices->findByProductId($id);

        return response()->json($prices);
    }
}

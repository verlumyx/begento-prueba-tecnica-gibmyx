<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Services\ProductsServices;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class ProductGetController extends Controller
{
    public function __construct() {}

    #[OA\Get(
        path: '/products/{id}',
        summary: 'Obtener un producto por ID',
        description: 'Retorna los datos de un producto específico mediante su ID.',
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
                description: 'Producto obtenido exitosamente',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 1),
                        new OA\Property(property: 'name', type: 'string', example: 'Laptop Gamer Asus ROG'),
                        new OA\Property(property: 'description', type: 'string', example: 'Laptop de alto rendimiento'),
                        new OA\Property(property: 'price', type: 'string', example: '1899.99'),
                        new OA\Property(property: 'currency_id', type: 'integer', example: 1),
                        new OA\Property(property: 'tax_cost', type: 'string', example: '303.99'),
                        new OA\Property(property: 'manufacturing_cost', type: 'string', example: '1150.00'),
                        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2026-10-25T21:42:30.000000Z'),
                        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2026-10-25T21:42:30.000000Z'),
                    ]
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
    public function __invoke(int $id, ProductsServices $productsServices): JsonResponse
    {
        $product = $productsServices->findById($id);

        return response()->json($product);
    }
}

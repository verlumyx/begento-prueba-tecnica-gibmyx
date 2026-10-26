<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Services\ProductsServices;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class ProductDeleteController extends Controller
{
    public function __construct() {}

    #[OA\Delete(
        path: '/products/{id}',
        summary: 'Eliminar un producto por ID',
        description: 'Elimina un producto y sus precios asociados mediante su ID.',
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
                description: 'Producto eliminado exitosamente',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Producto eliminado exitosamente.'),
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
        $productsServices->delete($id);

        return response()->json([
            'message' => 'Producto eliminado exitosamente.',
        ]);
    }
}

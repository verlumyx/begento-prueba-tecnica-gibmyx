<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Http\Requests\Products\StoreProductPriceRequest;
use App\Services\ProductPricesServices;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class ProductPricePostController extends Controller
{
    public function __construct() {}

    #[OA\Post(
        path: '/products/{id}/prices',
        summary: 'Crear un nuevo precio para un producto',
        description: 'Registra un nuevo precio para un producto en una divisa específica.',
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
        requestBody: new OA\RequestBody(
            required: true,
            description: 'Datos requeridos para registrar el precio de un producto en una divisa',
            content: new OA\JsonContent(
                required: ['currency_id', 'price'],
                properties: [
                    new OA\Property(property: 'currency_id', type: 'integer', example: 2),
                    new OA\Property(property: 'price', type: 'number', format: 'float', example: 1104.46),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Precio registrado exitosamente',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 1),
                        new OA\Property(property: 'product_id', type: 'integer', example: 1),
                        new OA\Property(property: 'currency_id', type: 'integer', example: 2),
                        new OA\Property(property: 'price', type: 'string', example: '1104.46'),
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
                response: 422,
                description: 'Error de validación en los datos enviados'
            ),
            new OA\Response(
                response: 500,
                description: 'Error interno del servidor'
            ),
        ]
    )]
    public function __invoke(int $id, StoreProductPriceRequest $request, ProductPricesServices $productPricesServices): JsonResponse
    {
        $price = $productPricesServices->create($id, $request->validated());

        return response()->json($price, 201);
    }
}

<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Http\Requests\Products\UpdateProductRequest;
use App\Services\ProductsServices;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class ProductPutController extends Controller
{
    public function __construct() {}

    #[OA\Put(
        path: '/products/{id}',
        summary: 'Actualizar un producto',
        description: 'Actualiza los datos de un producto existente mediante su ID.',
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
            description: 'Datos a actualizar del producto',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Laptop Gamer Pro Updated'),
                    new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Laptop de alto rendimiento actualizada'),
                    new OA\Property(property: 'price', type: 'number', format: 'float', example: 1299.99),
                    new OA\Property(property: 'currency_id', type: 'integer', example: 1),
                    new OA\Property(property: 'tax_cost', type: 'number', format: 'float', example: 207.99),
                    new OA\Property(property: 'manufacturing_cost', type: 'number', format: 'float', example: 800.00),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Producto actualizado exitosamente',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 1),
                        new OA\Property(property: 'name', type: 'string', example: 'Laptop Gamer Pro Updated'),
                        new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Laptop de alto rendimiento actualizada'),
                        new OA\Property(property: 'price', type: 'string', example: '1299.99'),
                        new OA\Property(property: 'currency_id', type: 'integer', example: 1),
                        new OA\Property(property: 'tax_cost', type: 'string', example: '207.99'),
                        new OA\Property(property: 'manufacturing_cost', type: 'string', example: '800.00'),
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
    public function __invoke(int $id, UpdateProductRequest $request, ProductsServices $productsServices): JsonResponse
    {
        $product = $productsServices->update($id, $request->validated());

        return response()->json($product);
    }
}

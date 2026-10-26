<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Http\Requests\Products\StoreProductRequest;
use App\Services\ProductsServices;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class ProductPostController extends Controller
{
    public function __construct() {}

    #[OA\Post(
        path: '/products',
        summary: 'Crear un nuevo producto',
        description: 'Registra un nuevo producto con su moneda base y costos asociados.',
        tags: ['Products'],
        requestBody: new OA\RequestBody(
            required: true,
            description: 'Datos requeridos para la creación de un producto',
            content: new OA\JsonContent(
                required: ['name', 'price', 'currency_id', 'tax_cost', 'manufacturing_cost'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Laptop Gamer Pro'),
                    new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Laptop de alto rendimiento'),
                    new OA\Property(property: 'price', type: 'number', format: 'float', example: 1200.50),
                    new OA\Property(property: 'currency_id', type: 'integer', example: 1),
                    new OA\Property(property: 'tax_cost', type: 'number', format: 'float', example: 192.08),
                    new OA\Property(property: 'manufacturing_cost', type: 'number', format: 'float', example: 750.00),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Producto creado exitosamente',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 1),
                        new OA\Property(property: 'name', type: 'string', example: 'Laptop Gamer Pro'),
                        new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Laptop de alto rendimiento'),
                        new OA\Property(property: 'price', type: 'string', example: '1200.50'),
                        new OA\Property(property: 'currency_id', type: 'integer', example: 1),
                        new OA\Property(property: 'tax_cost', type: 'string', example: '192.08'),
                        new OA\Property(property: 'manufacturing_cost', type: 'string', example: '750.00'),
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
    public function __invoke(StoreProductRequest $request, ProductsServices $productsServices): JsonResponse
    {
        $product = $productsServices->create($request->validated());

        return response()->json($product, 201);
    }
}

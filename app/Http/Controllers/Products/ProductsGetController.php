<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Services\ProductsServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class ProductsGetController extends Controller
{
    public function __construct() {}

    #[OA\Get(
        path: '/products',
        summary: 'Listar productos con paginación por cursor',
        description: 'Retorna el listado de productos paginado mediante cursor, optimizado para alto rendimiento.',
        tags: ['Products'],
        parameters: [
            new OA\Parameter(
                name: 'per_page',
                in: 'query',
                description: 'Cantidad de elementos por página (por defecto 15)',
                required: false,
                schema: new OA\Schema(type: 'integer', default: 15)
            ),
            new OA\Parameter(
                name: 'cursor',
                in: 'query',
                description: 'Cursor para navegar a la siguiente o anterior página',
                required: false,
                schema: new OA\Schema(type: 'string')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Listado de productos obtenido exitosamente con cursor',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(
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
                        new OA\Property(property: 'path', type: 'string', example: 'http://localhost:8090/api/products'),
                        new OA\Property(property: 'per_page', type: 'integer', example: 15),
                        new OA\Property(property: 'next_cursor', type: 'string', nullable: true, example: 'eyJpZCI6MTV9'),
                        new OA\Property(property: 'next_page_url', type: 'string', nullable: true, example: 'http://localhost:8090/api/products?cursor=eyJpZCI6MTV9'),
                        new OA\Property(property: 'prev_cursor', type: 'string', nullable: true, example: null),
                        new OA\Property(property: 'prev_page_url', type: 'string', nullable: true, example: null),
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Error interno del servidor'
            ),
        ]
    )]
    public function __invoke(Request $request, ProductsServices $productsServices): JsonResponse
    {
        $perPage = (int) $request->input('per_page', 15);
        if ($perPage <= 0) {
            $perPage = 15;
        }

        return response()->json($productsServices->cursorPaginate($perPage));
    }
}

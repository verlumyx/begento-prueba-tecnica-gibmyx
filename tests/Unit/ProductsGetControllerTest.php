<?php

namespace Tests\Unit;

use App\Http\Controllers\Products\ProductsGetController;
use App\Models\Product;
use App\Services\ProductsServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\CursorPaginator;
use Mockery;
use Tests\TestCase;

class ProductsGetControllerTest extends TestCase
{
    public function test_invoke_returns_json_response_with_cursor_paginator(): void
    {
        $product = new Product([
            'name' => 'Mouse Gamer',
            'description' => 'RGB mouse',
            'price' => 49.99,
            'currency_id' => 1,
            'tax_cost' => 8.00,
            'manufacturing_cost' => 20.00,
        ]);
        $product->id = 1;

        $paginator = new CursorPaginator(
            collect([$product]),
            15,
            null,
            ['path' => 'http://localhost/api/products']
        );

        $mockService = Mockery::mock(ProductsServices::class);
        $mockService->shouldReceive('cursorPaginate')
            ->once()
            ->with(15)
            ->andReturn($paginator);

        $request = Request::create('/api/products', 'GET');
        $controller = new ProductsGetController;
        $response = $controller($request, $mockService);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $responseData = $response->getData(true);
        $this->assertArrayHasKey('data', $responseData);
        $this->assertArrayHasKey('next_cursor', $responseData);
        $this->assertArrayHasKey('prev_cursor', $responseData);
        $this->assertCount(1, $responseData['data']);
        $this->assertEquals('Mouse Gamer', $responseData['data'][0]['name']);
    }

    public function test_invoke_uses_custom_per_page_parameter(): void
    {
        $paginator = new CursorPaginator(
            collect([]),
            5,
            null,
            ['path' => 'http://localhost/api/products']
        );

        $mockService = Mockery::mock(ProductsServices::class);
        $mockService->shouldReceive('cursorPaginate')
            ->once()
            ->with(5)
            ->andReturn($paginator);

        $request = Request::create('/api/products', 'GET', ['per_page' => 5]);
        $controller = new ProductsGetController;
        $response = $controller($request, $mockService);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertSame([], $response->getData(true)['data']);
    }

    public function test_invoke_falls_back_to_default_per_page_when_invalid_per_page_given(): void
    {
        $paginator = new CursorPaginator(
            collect([]),
            15,
            null,
            ['path' => 'http://localhost/api/products']
        );

        $mockService = Mockery::mock(ProductsServices::class);
        $mockService->shouldReceive('cursorPaginate')
            ->once()
            ->with(15)
            ->andReturn($paginator);

        $request = Request::create('/api/products', 'GET', ['per_page' => -1]);
        $controller = new ProductsGetController;
        $response = $controller($request, $mockService);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }
}

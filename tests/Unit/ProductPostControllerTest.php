<?php

namespace Tests\Unit;

use App\Http\Controllers\Products\ProductPostController;
use App\Http\Requests\Products\StoreProductRequest;
use App\Models\Product;
use App\Services\ProductsServices;
use Illuminate\Http\JsonResponse;
use Mockery;
use Tests\TestCase;

class ProductPostControllerTest extends TestCase
{
    public function test_invoke_creates_product_and_returns_created_json_response(): void
    {
        $payload = [
            'name' => 'Laptop Gamer Asus ROG',
            'description' => 'Laptop de alto rendimiento',
            'price' => 1899.99,
            'currency_id' => 1,
            'tax_cost' => 303.99,
            'manufacturing_cost' => 1150.00,
        ];

        $product = new Product($payload);
        $product->id = 1;

        $mockRequest = Mockery::mock(StoreProductRequest::class);
        $mockRequest->shouldReceive('validated')
            ->once()
            ->andReturn($payload);

        $mockService = Mockery::mock(ProductsServices::class);
        $mockService->shouldReceive('create')
            ->once()
            ->with($payload)
            ->andReturn($product);

        $controller = new ProductPostController;
        $response = $controller($mockRequest, $mockService);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals('Laptop Gamer Asus ROG', $response->getData(true)['name']);
        $this->assertEquals(1, $response->getData(true)['id']);
    }

    public function test_invoke_creates_product_with_null_description(): void
    {
        $payload = [
            'name' => 'Mouse Pad XL',
            'description' => null,
            'price' => 25.00,
            'currency_id' => 1,
            'tax_cost' => 4.00,
            'manufacturing_cost' => 10.00,
        ];

        $product = new Product(array_merge($payload, ['id' => 2]));

        $mockRequest = Mockery::mock(StoreProductRequest::class);
        $mockRequest->shouldReceive('validated')
            ->once()
            ->andReturn($payload);

        $mockService = Mockery::mock(ProductsServices::class);
        $mockService->shouldReceive('create')
            ->once()
            ->with($payload)
            ->andReturn($product);

        $controller = new ProductPostController;
        $response = $controller($mockRequest, $mockService);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertNull($response->getData(true)['description']);
    }
}

<?php

namespace Tests\Unit;

use App\Exceptions\ProductNotFoundException;
use App\Http\Controllers\Products\ProductGetController;
use App\Models\Product;
use App\Services\ProductsServices;
use Illuminate\Http\JsonResponse;
use Mockery;
use Tests\TestCase;

class ProductGetControllerTest extends TestCase
{
    public function test_invoke_returns_json_response_with_product_when_found(): void
    {
        $product = new Product([
            'name' => 'Laptop Gamer Pro',
            'description' => 'Laptop de alto rendimiento',
            'price' => 1200.50,
            'currency_id' => 1,
            'tax_cost' => 192.08,
            'manufacturing_cost' => 750.00,
        ]);
        $product->id = 1;

        $mockService = Mockery::mock(ProductsServices::class);
        $mockService->shouldReceive('findById')
            ->once()
            ->with(1)
            ->andReturn($product);

        $controller = new ProductGetController;
        $response = $controller(1, $mockService);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(1, $response->getData(true)['id']);
        $this->assertEquals('Laptop Gamer Pro', $response->getData(true)['name']);
    }

    public function test_invoke_throws_product_not_found_exception_when_product_does_not_exist(): void
    {
        $this->expectException(ProductNotFoundException::class);
        $this->expectExceptionMessage('El producto solicitado no fue encontrado.');

        $mockService = Mockery::mock(ProductsServices::class);
        $mockService->shouldReceive('findById')
            ->once()
            ->with(999)
            ->andThrow(new ProductNotFoundException);

        $controller = new ProductGetController;
        $controller(999, $mockService);
    }
}

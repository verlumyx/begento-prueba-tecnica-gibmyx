<?php

namespace Tests\Unit;

use App\Exceptions\ProductNotFoundException;
use App\Http\Controllers\Products\ProductPutController;
use App\Http\Requests\Products\UpdateProductRequest;
use App\Models\Product;
use App\Services\ProductsServices;
use Illuminate\Http\JsonResponse;
use Mockery;
use Tests\TestCase;

class ProductPutControllerTest extends TestCase
{
    public function test_invoke_updates_product_and_returns_json_response(): void
    {
        $payload = [
            'name' => 'Laptop Gamer Pro Updated',
            'description' => 'Updated Description',
            'price' => 1299.99,
            'currency_id' => 1,
            'tax_cost' => 207.99,
            'manufacturing_cost' => 800.00,
        ];

        $product = new Product($payload);
        $product->id = 1;

        $mockRequest = Mockery::mock(UpdateProductRequest::class);
        $mockRequest->shouldReceive('validated')
            ->once()
            ->andReturn($payload);

        $mockService = Mockery::mock(ProductsServices::class);
        $mockService->shouldReceive('update')
            ->once()
            ->with(1, $payload)
            ->andReturn($product);

        $controller = new ProductPutController;
        $response = $controller(1, $mockRequest, $mockService);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(1, $response->getData(true)['id']);
        $this->assertEquals('Laptop Gamer Pro Updated', $response->getData(true)['name']);
    }

    public function test_invoke_throws_product_not_found_exception_when_product_does_not_exist(): void
    {
        $this->expectException(ProductNotFoundException::class);
        $this->expectExceptionMessage('El producto solicitado no fue encontrado.');

        $payload = ['name' => 'Non Existent Product'];

        $mockRequest = Mockery::mock(UpdateProductRequest::class);
        $mockRequest->shouldReceive('validated')
            ->once()
            ->andReturn($payload);

        $mockService = Mockery::mock(ProductsServices::class);
        $mockService->shouldReceive('update')
            ->once()
            ->with(999, $payload)
            ->andThrow(new ProductNotFoundException);

        $controller = new ProductPutController;
        $controller(999, $mockRequest, $mockService);
    }
}

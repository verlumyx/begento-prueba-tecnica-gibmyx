<?php

namespace Tests\Unit;

use App\Exceptions\ProductNotFoundException;
use App\Http\Controllers\Products\ProductPricePostController;
use App\Http\Requests\Products\StoreProductPriceRequest;
use App\Models\ProductPrice;
use App\Services\ProductPricesServices;
use Illuminate\Http\JsonResponse;
use Mockery;
use Tests\TestCase;

class ProductPricePostControllerTest extends TestCase
{
    public function test_invoke_creates_price_and_returns_created_json_response(): void
    {
        $payload = [
            'currency_id' => 2,
            'price' => 1104.46,
        ];

        $productPrice = new ProductPrice(array_merge($payload, [
            'id' => 1,
            'product_id' => 1,
        ]));
        $productPrice->id = 1;

        $mockRequest = Mockery::mock(StoreProductPriceRequest::class);
        $mockRequest->shouldReceive('validated')
            ->once()
            ->andReturn($payload);

        $mockService = Mockery::mock(ProductPricesServices::class);
        $mockService->shouldReceive('create')
            ->once()
            ->with(1, $payload)
            ->andReturn($productPrice);

        $controller = new ProductPricePostController;
        $response = $controller(1, $mockRequest, $mockService);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals(1, $response->getData(true)['id']);
        $this->assertEquals(1, $response->getData(true)['product_id']);
        $this->assertEquals(2, $response->getData(true)['currency_id']);
        $this->assertEquals(1104.46, $response->getData(true)['price']);
    }

    public function test_invoke_throws_product_not_found_exception_when_product_does_not_exist(): void
    {
        $this->expectException(ProductNotFoundException::class);
        $this->expectExceptionMessage('El producto solicitado no fue encontrado.');

        $payload = [
            'currency_id' => 2,
            'price' => 1104.46,
        ];

        $mockRequest = Mockery::mock(StoreProductPriceRequest::class);
        $mockRequest->shouldReceive('validated')
            ->once()
            ->andReturn($payload);

        $mockService = Mockery::mock(ProductPricesServices::class);
        $mockService->shouldReceive('create')
            ->once()
            ->with(999, $payload)
            ->andThrow(new ProductNotFoundException);

        $controller = new ProductPricePostController;
        $controller(999, $mockRequest, $mockService);
    }
}

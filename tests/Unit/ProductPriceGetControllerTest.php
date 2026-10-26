<?php

namespace Tests\Unit;

use App\Exceptions\ProductNotFoundException;
use App\Http\Controllers\Products\ProductPriceGetController;
use App\Models\ProductPrice;
use App\Services\ProductPricesServices;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Mockery;
use Tests\TestCase;

class ProductPriceGetControllerTest extends TestCase
{
    public function test_invoke_returns_json_response_with_prices_when_product_found(): void
    {
        $price = new ProductPrice([
            'id' => 1,
            'product_id' => 1,
            'currency_id' => 2,
            'price' => 1104.46,
        ]);
        $price->id = 1;

        $collection = new Collection([$price]);

        $mockService = Mockery::mock(ProductPricesServices::class);
        $mockService->shouldReceive('findByProductId')
            ->once()
            ->with(1)
            ->andReturn($collection);

        $controller = new ProductPriceGetController;
        $response = $controller(1, $mockService);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->getData(true);
        $this->assertIsArray($data);
        $this->assertCount(1, $data);
        $this->assertEquals(1, $data[0]['id']);
        $this->assertEquals(1, $data[0]['product_id']);
        $this->assertEquals(2, $data[0]['currency_id']);
    }

    public function test_invoke_throws_product_not_found_exception_when_product_does_not_exist(): void
    {
        $this->expectException(ProductNotFoundException::class);
        $this->expectExceptionMessage('El producto solicitado no fue encontrado.');

        $mockService = Mockery::mock(ProductPricesServices::class);
        $mockService->shouldReceive('findByProductId')
            ->once()
            ->with(999)
            ->andThrow(new ProductNotFoundException);

        $controller = new ProductPriceGetController;
        $controller(999, $mockService);
    }
}

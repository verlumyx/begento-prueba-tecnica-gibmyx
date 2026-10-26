<?php

namespace Tests\Unit;

use App\Exceptions\ProductNotFoundException;
use App\Http\Controllers\Products\ProductDeleteController;
use App\Services\ProductsServices;
use Illuminate\Http\JsonResponse;
use Mockery;
use Tests\TestCase;

class ProductDeleteControllerTest extends TestCase
{
    public function test_invoke_deletes_product_and_returns_success_json_response(): void
    {
        $mockService = Mockery::mock(ProductsServices::class);
        $mockService->shouldReceive('delete')
            ->once()
            ->with(1)
            ->andReturn(true);

        $controller = new ProductDeleteController;
        $response = $controller(1, $mockService);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals([
            'message' => 'Producto eliminado exitosamente.',
        ], $response->getData(true));
    }

    public function test_invoke_throws_product_not_found_exception_when_product_does_not_exist(): void
    {
        $this->expectException(ProductNotFoundException::class);
        $this->expectExceptionMessage('El producto solicitado no fue encontrado.');

        $mockService = Mockery::mock(ProductsServices::class);
        $mockService->shouldReceive('delete')
            ->once()
            ->with(999)
            ->andThrow(new ProductNotFoundException);

        $controller = new ProductDeleteController;
        $controller(999, $mockService);
    }
}

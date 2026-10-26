<?php

namespace Tests\Unit;

use App\Exceptions\CurrencyNotFoundException;
use App\Http\Controllers\Currencies\CurrencyDeleteController;
use App\Services\CurrenciesServices;
use Illuminate\Http\JsonResponse;
use Mockery;
use Tests\TestCase;

class CurrencyDeleteControllerTest extends TestCase
{
    public function test_invoke_deletes_currency_and_returns_success_json_response(): void
    {
        $mockService = Mockery::mock(CurrenciesServices::class);
        $mockService->shouldReceive('delete')
            ->once()
            ->with(1)
            ->andReturn(true);

        $controller = new CurrencyDeleteController;
        $response = $controller(1, $mockService);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals([
            'message' => 'Divisa eliminada exitosamente.',
        ], $response->getData(true));
    }

    public function test_invoke_throws_currency_not_found_exception_when_currency_does_not_exist(): void
    {
        $this->expectException(CurrencyNotFoundException::class);
        $this->expectExceptionMessage('La divisa solicitada no fue encontrada.');

        $mockService = Mockery::mock(CurrenciesServices::class);
        $mockService->shouldReceive('delete')
            ->once()
            ->with(999)
            ->andThrow(new CurrencyNotFoundException);

        $controller = new CurrencyDeleteController;
        $controller(999, $mockService);
    }
}

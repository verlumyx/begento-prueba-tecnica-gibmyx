<?php

namespace Tests\Unit;

use App\Exceptions\CurrencyNotFoundException;
use App\Http\Controllers\Currencies\CurrencyGetController;
use App\Models\Currency;
use App\Services\CurrenciesServices;
use Illuminate\Http\JsonResponse;
use Mockery;
use Tests\TestCase;

class CurrencyGetControllerTest extends TestCase
{
    public function test_invoke_returns_json_response_with_currency_when_found(): void
    {
        $currency = new Currency([
            'name' => 'Euro',
            'symbol' => '€',
            'exchange_rate' => 0.9200,
        ]);
        $currency->id = 1;

        $mockService = Mockery::mock(CurrenciesServices::class);
        $mockService->shouldReceive('findById')
            ->once()
            ->with(1)
            ->andReturn($currency);

        $controller = new CurrencyGetController;
        $response = $controller(1, $mockService);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(1, $response->getData(true)['id']);
        $this->assertEquals('Euro', $response->getData(true)['name']);
    }

    public function test_invoke_throws_currency_not_found_exception_when_currency_does_not_exist(): void
    {
        $this->expectException(CurrencyNotFoundException::class);
        $this->expectExceptionMessage('La divisa solicitada no fue encontrada.');

        $mockService = Mockery::mock(CurrenciesServices::class);
        $mockService->shouldReceive('findById')
            ->once()
            ->with(999)
            ->andThrow(new CurrencyNotFoundException);

        $controller = new CurrencyGetController;
        $controller(999, $mockService);
    }
}

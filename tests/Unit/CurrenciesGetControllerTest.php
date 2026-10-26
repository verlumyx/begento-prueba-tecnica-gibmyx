<?php

namespace Tests\Unit;

use App\Http\Controllers\Currencies\CurrenciesGetController;
use App\Services\CurrenciesServices;
use Illuminate\Http\JsonResponse;
use Mockery;
use Tests\TestCase;

class CurrenciesGetControllerTest extends TestCase
{
    public function test_invoke_returns_json_response_with_currencies_from_service(): void
    {
        $currenciesData = collect([
            [
                'id' => 1,
                'name' => 'US Dollar',
                'symbol' => '$',
                'exchange_rate' => '1.0000',
            ],
            [
                'id' => 2,
                'name' => 'Euro',
                'symbol' => '€',
                'exchange_rate' => '0.9200',
            ],
        ]);

        $mockService = Mockery::mock(CurrenciesServices::class);
        $mockService->shouldReceive('all')
            ->once()
            ->andReturn($currenciesData);

        $controller = new CurrenciesGetController();
        $response = $controller($mockService);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($currenciesData->toArray(), $response->getData(true));
    }

    public function test_invoke_returns_empty_json_response_when_service_has_no_currencies(): void
    {
        $mockService = Mockery::mock(CurrenciesServices::class);
        $mockService->shouldReceive('all')
            ->once()
            ->andReturn(collect([]));

        $controller = new CurrenciesGetController();
        $response = $controller($mockService);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertSame([], $response->getData(true));
    }
}

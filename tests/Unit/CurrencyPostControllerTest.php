<?php

namespace Tests\Unit;

use App\Http\Controllers\Currencies\CurrencyPostController;
use App\Http\Requests\Currencies\StoreCurrencyRequest;
use App\Models\Currency;
use App\Services\CurrenciesServices;
use Illuminate\Http\JsonResponse;
use Mockery;
use Tests\TestCase;

class CurrencyPostControllerTest extends TestCase
{
    public function test_invoke_creates_currency_and_returns_created_json_response(): void
    {
        $payload = [
            'name' => 'US Dollar',
            'symbol' => '$',
            'exchange_rate' => 1.0000,
        ];

        $currency = new Currency($payload);
        $currency->id = 1;

        $mockRequest = Mockery::mock(StoreCurrencyRequest::class);
        $mockRequest->shouldReceive('validated')
            ->once()
            ->andReturn($payload);

        $mockService = Mockery::mock(CurrenciesServices::class);
        $mockService->shouldReceive('create')
            ->once()
            ->with($payload)
            ->andReturn($currency);

        $controller = new CurrencyPostController;
        $response = $controller($mockRequest, $mockService);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals(1, $response->getData(true)['id']);
        $this->assertEquals('US Dollar', $response->getData(true)['name']);
        $this->assertEquals('$', $response->getData(true)['symbol']);
    }
}

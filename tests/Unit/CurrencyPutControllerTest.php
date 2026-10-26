<?php

namespace Tests\Unit;

use App\Exceptions\CurrencyNotFoundException;
use App\Http\Controllers\Currencies\CurrencyPutController;
use App\Http\Requests\Currencies\UpdateCurrencyRequest;
use App\Models\Currency;
use App\Services\CurrenciesServices;
use Illuminate\Http\JsonResponse;
use Mockery;
use Tests\TestCase;

class CurrencyPutControllerTest extends TestCase
{
    public function test_invoke_updates_currency_and_returns_json_response(): void
    {
        $payload = [
            'name' => 'Euro Updated',
            'symbol' => '€',
            'exchange_rate' => 0.9500,
        ];

        $currency = new Currency($payload);
        $currency->id = 1;

        $mockRequest = Mockery::mock(UpdateCurrencyRequest::class);
        $mockRequest->shouldReceive('validated')
            ->once()
            ->andReturn($payload);

        $mockService = Mockery::mock(CurrenciesServices::class);
        $mockService->shouldReceive('update')
            ->once()
            ->with(1, $payload)
            ->andReturn($currency);

        $controller = new CurrencyPutController;
        $response = $controller(1, $mockRequest, $mockService);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(1, $response->getData(true)['id']);
        $this->assertEquals('Euro Updated', $response->getData(true)['name']);
    }

    public function test_invoke_throws_currency_not_found_exception_when_currency_does_not_exist(): void
    {
        $this->expectException(CurrencyNotFoundException::class);
        $this->expectExceptionMessage('La divisa solicitada no fue encontrada.');

        $payload = ['name' => 'Non Existent'];

        $mockRequest = Mockery::mock(UpdateCurrencyRequest::class);
        $mockRequest->shouldReceive('validated')
            ->once()
            ->andReturn($payload);

        $mockService = Mockery::mock(CurrenciesServices::class);
        $mockService->shouldReceive('update')
            ->once()
            ->with(999, $payload)
            ->andThrow(new CurrencyNotFoundException);

        $controller = new CurrencyPutController;
        $controller(999, $mockRequest, $mockService);
    }
}

<?php

namespace Tests\Unit;

use App\Exceptions\CurrencyNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Tests\TestCase;

class CurrencyNotFoundExceptionTest extends TestCase
{
    public function test_exception_contains_correct_message_and_status_code(): void
    {
        $exception = new CurrencyNotFoundException;

        $this->assertEquals('La divisa solicitada no fue encontrada.', $exception->getMessage());
        $this->assertEquals(404, $exception->getCode());
    }

    public function test_render_returns_json_response_with_404_status(): void
    {
        $exception = new CurrencyNotFoundException;
        $request = Request::create('/api/currencies/99', 'GET');

        $response = $exception->render($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals([
            'message' => 'La divisa solicitada no fue encontrada.',
        ], $response->getData(true));
    }
}

<?php

namespace Tests\Unit;

use App\Exceptions\ProductNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Tests\TestCase;

class ProductNotFoundExceptionTest extends TestCase
{
    public function test_exception_contains_correct_message_and_status_code(): void
    {
        $exception = new ProductNotFoundException;

        $this->assertEquals('El producto solicitado no fue encontrado.', $exception->getMessage());
        $this->assertEquals(404, $exception->getCode());
    }

    public function test_render_returns_json_response_with_404_status(): void
    {
        $exception = new ProductNotFoundException;
        $request = Request::create('/api/products/99', 'GET');

        $response = $exception->render($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals([
            'message' => 'El producto solicitado no fue encontrado.',
        ], $response->getData(true));
    }
}

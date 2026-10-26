<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CurrencyNotFoundException extends Exception
{
    public function __construct(string $message = 'La divisa solicitada no fue encontrada.', int $code = 404)
    {
        parent::__construct($message, $code);
    }

    /**
     * Render the exception into an HTTP response.
     */
    public function render(Request $request): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
        ], 404);
    }
}

<?php

namespace Tests\Unit;

use App\Http\Requests\Currencies\UpdateCurrencyRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class UpdateCurrencyRequestTest extends TestCase
{
    private function getRules(): array
    {
        return (new UpdateCurrencyRequest)->rules();
    }

    public function test_authorize_returns_true(): void
    {
        $request = new UpdateCurrencyRequest;
        $this->assertTrue($request->authorize());
    }

    public function test_valid_full_data_passes_validation(): void
    {
        $data = [
            'name' => 'Euro Updated',
            'symbol' => '€',
            'exchange_rate' => 0.9500,
        ];

        $validator = Validator::make($data, $this->getRules());

        $this->assertTrue($validator->passes());
    }

    public function test_valid_partial_data_passes_validation(): void
    {
        $data = [
            'exchange_rate' => 0.9500,
        ];

        $validator = Validator::make($data, $this->getRules());

        $this->assertTrue($validator->passes());
    }

    public function test_invalid_symbol_fails_validation(): void
    {
        $data = [
            'symbol' => '12345678901',
        ];

        $validator = Validator::make($data, $this->getRules());

        $this->assertFalse($validator->passes());
        $this->assertTrue($validator->errors()->has('symbol'));
    }

    public function test_invalid_exchange_rate_fails_validation(): void
    {
        $data = [
            'exchange_rate' => -5,
        ];

        $validator = Validator::make($data, $this->getRules());

        $this->assertFalse($validator->passes());
        $this->assertTrue($validator->errors()->has('exchange_rate'));
    }
}

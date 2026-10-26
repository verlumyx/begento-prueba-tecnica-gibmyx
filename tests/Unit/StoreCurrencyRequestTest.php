<?php

namespace Tests\Unit;

use App\Http\Requests\Currencies\StoreCurrencyRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class StoreCurrencyRequestTest extends TestCase
{
    private function getRules(): array
    {
        return (new StoreCurrencyRequest)->rules();
    }

    public function test_authorize_returns_true(): void
    {
        $request = new StoreCurrencyRequest;
        $this->assertTrue($request->authorize());
    }

    public function test_valid_data_passes_validation(): void
    {
        $data = [
            'name' => 'US Dollar',
            'symbol' => '$',
            'exchange_rate' => 1.0000,
        ];

        $validator = Validator::make($data, $this->getRules());

        $this->assertTrue($validator->passes());
    }

    public function test_required_fields_validation(): void
    {
        $validator = Validator::make([], $this->getRules());

        $this->assertFalse($validator->passes());
        $errors = $validator->errors();

        $this->assertTrue($errors->has('name'));
        $this->assertTrue($errors->has('symbol'));
        $this->assertTrue($errors->has('exchange_rate'));
    }

    public function test_symbol_max_length_validation(): void
    {
        $data = [
            'name' => 'Invalid Currency',
            'symbol' => '12345678901',
            'exchange_rate' => 1.0000,
        ];

        $validator = Validator::make($data, $this->getRules());

        $this->assertFalse($validator->passes());
        $this->assertTrue($validator->errors()->has('symbol'));
    }

    public function test_exchange_rate_must_be_numeric_and_positive(): void
    {
        $data = [
            'name' => 'US Dollar',
            'symbol' => '$',
            'exchange_rate' => 0,
        ];

        $validator = Validator::make($data, $this->getRules());
        $this->assertFalse($validator->passes());
        $this->assertTrue($validator->errors()->has('exchange_rate'));

        $data['exchange_rate'] = -1.5;
        $validator = Validator::make($data, $this->getRules());
        $this->assertFalse($validator->passes());
        $this->assertTrue($validator->errors()->has('exchange_rate'));
    }
}

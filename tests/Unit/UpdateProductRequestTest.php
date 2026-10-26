<?php

namespace Tests\Unit;

use App\Http\Requests\Products\UpdateProductRequest;
use App\Models\Currency;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class UpdateProductRequestTest extends TestCase
{
    use RefreshDatabase;

    private function getRules(): array
    {
        return (new UpdateProductRequest)->rules();
    }

    public function test_authorize_returns_true(): void
    {
        $request = new UpdateProductRequest;
        $this->assertTrue($request->authorize());
    }

    public function test_valid_full_data_passes_validation(): void
    {
        $currency = Currency::factory()->create();

        $data = [
            'name' => 'Updated Product Name',
            'description' => 'Updated description',
            'price' => 199.99,
            'currency_id' => $currency->id,
            'tax_cost' => 32.00,
            'manufacturing_cost' => 100.00,
        ];

        $validator = Validator::make($data, $this->getRules());

        $this->assertTrue($validator->passes());
    }

    public function test_valid_partial_data_passes_validation(): void
    {
        $data = [
            'price' => 249.50,
        ];

        $validator = Validator::make($data, $this->getRules());

        $this->assertTrue($validator->passes());
    }

    public function test_empty_data_passes_validation(): void
    {
        $validator = Validator::make([], $this->getRules());

        $this->assertTrue($validator->passes());
    }

    public function test_nullable_description_passes_validation(): void
    {
        $data = [
            'description' => null,
        ];

        $validator = Validator::make($data, $this->getRules());

        $this->assertTrue($validator->passes());
    }

    public function test_invalid_name_fails_validation(): void
    {
        $data = [
            'name' => str_repeat('a', 256),
        ];

        $validator = Validator::make($data, $this->getRules());

        $this->assertFalse($validator->passes());
        $this->assertTrue($validator->errors()->has('name'));
    }

    public function test_invalid_price_fails_validation(): void
    {
        $data = [
            'price' => -10,
        ];

        $validator = Validator::make($data, $this->getRules());

        $this->assertFalse($validator->passes());
        $this->assertTrue($validator->errors()->has('price'));
    }

    public function test_invalid_currency_id_fails_validation(): void
    {
        $data = [
            'currency_id' => 99999,
        ];

        $validator = Validator::make($data, $this->getRules());

        $this->assertFalse($validator->passes());
        $this->assertTrue($validator->errors()->has('currency_id'));
    }

    public function test_invalid_tax_cost_fails_validation(): void
    {
        $data = [
            'tax_cost' => -5.00,
        ];

        $validator = Validator::make($data, $this->getRules());

        $this->assertFalse($validator->passes());
        $this->assertTrue($validator->errors()->has('tax_cost'));
    }

    public function test_invalid_manufacturing_cost_fails_validation(): void
    {
        $data = [
            'manufacturing_cost' => -20.00,
        ];

        $validator = Validator::make($data, $this->getRules());

        $this->assertFalse($validator->passes());
        $this->assertTrue($validator->errors()->has('manufacturing_cost'));
    }
}

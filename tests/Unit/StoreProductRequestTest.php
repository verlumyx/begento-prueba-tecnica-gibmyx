<?php

namespace Tests\Unit;

use App\Http\Requests\Products\StoreProductRequest;
use App\Models\Currency;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class StoreProductRequestTest extends TestCase
{
    use RefreshDatabase;

    private function getRules(): array
    {
        return (new StoreProductRequest)->rules();
    }

    public function test_authorize_returns_true(): void
    {
        $request = new StoreProductRequest;
        $this->assertTrue($request->authorize());
    }

    public function test_valid_data_passes_validation(): void
    {
        $currency = Currency::factory()->create();

        $data = [
            'name' => 'Valid Product',
            'description' => 'A valid description',
            'price' => 99.99,
            'currency_id' => $currency->id,
            'tax_cost' => 15.00,
            'manufacturing_cost' => 45.00,
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
        $this->assertTrue($errors->has('price'));
        $this->assertTrue($errors->has('currency_id'));
        $this->assertTrue($errors->has('tax_cost'));
        $this->assertTrue($errors->has('manufacturing_cost'));
    }

    public function test_price_must_be_numeric_and_positive(): void
    {
        $currency = Currency::factory()->create();

        $data = [
            'name' => 'Valid Product',
            'price' => -10,
            'currency_id' => $currency->id,
            'tax_cost' => 15.00,
            'manufacturing_cost' => 45.00,
        ];

        $validator = Validator::make($data, $this->getRules());
        $this->assertFalse($validator->passes());
        $this->assertTrue($validator->errors()->has('price'));
    }

    public function test_currency_must_exist_in_database(): void
    {
        $data = [
            'name' => 'Valid Product',
            'price' => 100,
            'currency_id' => 99999,
            'tax_cost' => 15.00,
            'manufacturing_cost' => 45.00,
        ];

        $validator = Validator::make($data, $this->getRules());
        $this->assertFalse($validator->passes());
        $this->assertTrue($validator->errors()->has('currency_id'));
    }
}

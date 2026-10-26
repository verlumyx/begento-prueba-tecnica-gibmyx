<?php

namespace Tests\Unit;

use App\Http\Requests\Products\StoreProductPriceRequest;
use App\Models\Currency;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class StoreProductPriceRequestTest extends TestCase
{
    use RefreshDatabase;

    private function getRules(): array
    {
        return (new StoreProductPriceRequest)->rules();
    }

    public function test_authorize_returns_true(): void
    {
        $request = new StoreProductPriceRequest;
        $this->assertTrue($request->authorize());
    }

    public function test_valid_data_passes_validation(): void
    {
        $currency = Currency::factory()->create();

        $data = [
            'currency_id' => $currency->id,
            'price' => 1104.46,
        ];

        $validator = Validator::make($data, $this->getRules());

        $this->assertTrue($validator->passes());
    }

    public function test_required_fields_validation(): void
    {
        $validator = Validator::make([], $this->getRules());

        $this->assertFalse($validator->passes());
        $errors = $validator->errors();

        $this->assertTrue($errors->has('currency_id'));
        $this->assertTrue($errors->has('price'));
    }

    public function test_price_must_be_numeric_and_positive(): void
    {
        $currency = Currency::factory()->create();

        $data = [
            'currency_id' => $currency->id,
            'price' => -10,
        ];

        $validator = Validator::make($data, $this->getRules());
        $this->assertFalse($validator->passes());
        $this->assertTrue($validator->errors()->has('price'));
    }

    public function test_currency_must_exist_in_database(): void
    {
        $data = [
            'currency_id' => 99999,
            'price' => 100,
        ];

        $validator = Validator::make($data, $this->getRules());
        $this->assertFalse($validator->passes());
        $this->assertTrue($validator->errors()->has('currency_id'));
    }
}

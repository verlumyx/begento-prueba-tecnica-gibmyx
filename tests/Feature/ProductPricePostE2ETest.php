<?php

namespace Tests\Feature;

use App\Models\Currency;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductPricePostE2ETest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_product_price_successfully_e2e(): void
    {
        $currency1 = Currency::factory()->create([
            'name' => 'US Dollar',
            'symbol' => '$',
            'exchange_rate' => 1.0000,
        ]);

        $currency2 = Currency::factory()->create([
            'name' => 'Euro',
            'symbol' => '€',
            'exchange_rate' => 0.9200,
        ]);

        $product = Product::factory()->create([
            'name' => 'Laptop Gamer Asus ROG',
            'currency_id' => $currency1->id,
            'price' => 1899.99,
        ]);

        $payload = [
            'currency_id' => $currency2->id,
            'price' => 1747.99,
        ];

        $response = $this->postJson("/api/products/{$product->id}/prices", $payload);

        $response->assertStatus(201)
            ->assertHeader('Content-Type', 'application/json')
            ->assertJsonStructure([
                'id',
                'product_id',
                'currency_id',
                'price',
                'created_at',
                'updated_at',
            ])
            ->assertJson([
                'product_id' => $product->id,
                'currency_id' => $currency2->id,
                'price' => '1747.99',
            ]);

        $this->assertDatabaseHas('product_prices', [
            'product_id' => $product->id,
            'currency_id' => $currency2->id,
            'price' => 1747.99,
        ]);
    }

    public function test_returns_404_with_custom_exception_message_when_product_does_not_exist_e2e(): void
    {
        $currency = Currency::factory()->create();

        $payload = [
            'currency_id' => $currency->id,
            'price' => 100.00,
        ];

        $response = $this->postJson('/api/products/99999/prices', $payload);

        $response->assertStatus(404)
            ->assertHeader('Content-Type', 'application/json')
            ->assertJson([
                'message' => 'El producto solicitado no fue encontrado.',
            ]);
    }

    public function test_validation_errors_when_required_fields_are_missing_e2e(): void
    {
        $currency = Currency::factory()->create();
        $product = Product::factory()->create(['currency_id' => $currency->id]);

        $response = $this->postJson("/api/products/{$product->id}/prices", []);

        $response->assertStatus(422)
            ->assertHeader('Content-Type', 'application/json')
            ->assertJsonValidationErrors([
                'currency_id',
                'price',
            ]);
    }

    public function test_validation_error_when_currency_id_does_not_exist_e2e(): void
    {
        $currency = Currency::factory()->create();
        $product = Product::factory()->create(['currency_id' => $currency->id]);

        $payload = [
            'currency_id' => 99999,
            'price' => 100.00,
        ];

        $response = $this->postJson("/api/products/{$product->id}/prices", $payload);

        $response->assertStatus(422)
            ->assertHeader('Content-Type', 'application/json')
            ->assertJsonValidationErrors(['currency_id']);
    }

    public function test_validation_error_when_price_is_negative_e2e(): void
    {
        $currency = Currency::factory()->create();
        $product = Product::factory()->create(['currency_id' => $currency->id]);

        $payload = [
            'currency_id' => $currency->id,
            'price' => -50.00,
        ];

        $response = $this->postJson("/api/products/{$product->id}/prices", $payload);

        $response->assertStatus(422)
            ->assertHeader('Content-Type', 'application/json')
            ->assertJsonValidationErrors(['price']);
    }

    public function test_non_numeric_id_returns_404_on_post_e2e(): void
    {
        $currency = Currency::factory()->create();

        $payload = [
            'currency_id' => $currency->id,
            'price' => 100.00,
        ];

        $response = $this->postJson('/api/products/abc/prices', $payload);

        $response->assertStatus(404);
    }
}

<?php

namespace Tests\Feature;

use App\Models\Currency;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductPutE2ETest extends TestCase
{
    use RefreshDatabase;

    public function test_can_update_product_e2e(): void
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
            'description' => 'Original description',
            'price' => 1899.99,
            'currency_id' => $currency1->id,
            'tax_cost' => 303.99,
            'manufacturing_cost' => 1150.00,
        ]);

        $payload = [
            'name' => 'Laptop Gamer Asus ROG Strix SCAR 18',
            'description' => 'Updated high performance gaming laptop',
            'price' => 2499.50,
            'currency_id' => $currency2->id,
            'tax_cost' => 399.92,
            'manufacturing_cost' => 1400.00,
        ];

        $response = $this->putJson("/api/products/{$product->id}", $payload);

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'application/json')
            ->assertJsonStructure([
                'id',
                'name',
                'description',
                'price',
                'currency_id',
                'tax_cost',
                'manufacturing_cost',
                'created_at',
                'updated_at',
            ])
            ->assertJson([
                'id' => $product->id,
                'name' => 'Laptop Gamer Asus ROG Strix SCAR 18',
                'description' => 'Updated high performance gaming laptop',
                'price' => '2499.50',
                'currency_id' => $currency2->id,
                'tax_cost' => '399.92',
                'manufacturing_cost' => '1400.00',
            ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Laptop Gamer Asus ROG Strix SCAR 18',
            'description' => 'Updated high performance gaming laptop',
            'currency_id' => $currency2->id,
            'price' => 2499.50,
            'tax_cost' => 399.92,
            'manufacturing_cost' => 1400.00,
        ]);
    }

    public function test_can_update_product_partial_fields_e2e(): void
    {
        $currency = Currency::factory()->create();

        $product = Product::factory()->create([
            'name' => 'Teclado Mecanico RGB',
            'description' => 'Switches Red',
            'price' => 120.00,
            'currency_id' => $currency->id,
            'tax_cost' => 19.20,
            'manufacturing_cost' => 50.00,
        ]);

        $payload = [
            'price' => 135.00,
            'description' => null,
        ];

        $response = $this->putJson("/api/products/{$product->id}", $payload);

        $response->assertStatus(200)
            ->assertJson([
                'id' => $product->id,
                'name' => 'Teclado Mecanico RGB',
                'description' => null,
                'price' => '135.00',
                'currency_id' => $currency->id,
                'tax_cost' => '19.20',
                'manufacturing_cost' => '50.00',
            ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Teclado Mecanico RGB',
            'description' => null,
            'price' => 135.00,
        ]);
    }

    public function test_returns_404_with_custom_exception_message_when_product_does_not_exist_e2e(): void
    {
        $response = $this->putJson('/api/products/99999', [
            'name' => 'Non Existent Product',
        ]);

        $response->assertStatus(404)
            ->assertHeader('Content-Type', 'application/json')
            ->assertJson([
                'message' => 'El producto solicitado no fue encontrado.',
            ]);
    }

    public function test_validation_errors_when_invalid_data_on_update_e2e(): void
    {
        $currency = Currency::factory()->create();
        $product = Product::factory()->create(['currency_id' => $currency->id]);

        $payload = [
            'name' => str_repeat('a', 256),
            'price' => -50.00,
            'currency_id' => 99999,
            'tax_cost' => -10.00,
            'manufacturing_cost' => -20.00,
        ];

        $response = $this->putJson("/api/products/{$product->id}", $payload);

        $response->assertStatus(422)
            ->assertHeader('Content-Type', 'application/json')
            ->assertJsonValidationErrors([
                'name',
                'price',
                'currency_id',
                'tax_cost',
                'manufacturing_cost',
            ]);
    }

    public function test_non_numeric_id_returns_404_on_update_e2e(): void
    {
        $response = $this->putJson('/api/products/abc', [
            'name' => 'Test',
        ]);

        $response->assertStatus(404);
    }
}

<?php

namespace Tests\Feature;

use App\Models\Currency;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductGetE2ETest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_product_by_id_e2e(): void
    {
        $currency = Currency::factory()->create([
            'name' => 'US Dollar',
            'symbol' => '$',
            'exchange_rate' => 1.0000,
        ]);

        $product = Product::factory()->create([
            'name' => 'Monitor Gamer 27 144Hz',
            'description' => 'Monitor IPS con 1ms de respuesta',
            'price' => 299.99,
            'currency_id' => $currency->id,
            'tax_cost' => 48.00,
            'manufacturing_cost' => 130.00,
        ]);

        $response = $this->getJson("/api/products/{$product->id}");

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
                'name' => 'Monitor Gamer 27 144Hz',
                'description' => 'Monitor IPS con 1ms de respuesta',
                'price' => '299.99',
                'currency_id' => $currency->id,
                'tax_cost' => '48.00',
                'manufacturing_cost' => '130.00',
            ]);
    }

    public function test_returns_404_with_custom_exception_message_when_product_does_not_exist_e2e(): void
    {
        $response = $this->getJson('/api/products/99999');

        $response->assertStatus(404)
            ->assertHeader('Content-Type', 'application/json')
            ->assertJson([
                'message' => 'El producto solicitado no fue encontrado.',
            ]);
    }

    public function test_non_numeric_id_returns_404_e2e(): void
    {
        $response = $this->getJson('/api/products/abc');

        $response->assertStatus(404);
    }
}

<?php

namespace Tests\Feature;

use App\Models\Currency;
use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductPriceGetE2ETest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_product_prices_e2e(): void
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

        $price1 = ProductPrice::factory()->create([
            'product_id' => $product->id,
            'currency_id' => $currency1->id,
            'price' => 1899.99,
        ]);

        $price2 = ProductPrice::factory()->create([
            'product_id' => $product->id,
            'currency_id' => $currency2->id,
            'price' => 1747.99,
        ]);

        $response = $this->getJson("/api/products/{$product->id}/prices");

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'application/json')
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'product_id',
                    'currency_id',
                    'price',
                    'created_at',
                    'updated_at',
                ],
            ])
            ->assertJsonCount(2)
            ->assertJsonFragment([
                'id' => $price1->id,
                'product_id' => $product->id,
                'currency_id' => $currency1->id,
                'price' => '1899.99',
            ])
            ->assertJsonFragment([
                'id' => $price2->id,
                'product_id' => $product->id,
                'currency_id' => $currency2->id,
                'price' => '1747.99',
            ]);
    }

    public function test_returns_empty_array_when_product_has_no_prices_e2e(): void
    {
        $currency = Currency::factory()->create();
        $product = Product::factory()->create(['currency_id' => $currency->id]);

        $response = $this->getJson("/api/products/{$product->id}/prices");

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'application/json')
            ->assertExactJson([]);
    }

    public function test_returns_404_with_custom_exception_message_when_product_does_not_exist_e2e(): void
    {
        $response = $this->getJson('/api/products/99999/prices');

        $response->assertStatus(404)
            ->assertHeader('Content-Type', 'application/json')
            ->assertJson([
                'message' => 'El producto solicitado no fue encontrado.',
            ]);
    }

    public function test_non_numeric_id_returns_404_e2e(): void
    {
        $response = $this->getJson('/api/products/abc/prices');

        $response->assertStatus(404);
    }
}

<?php

namespace Tests\Feature;

use App\Models\Currency;
use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CurrencyDeleteE2ETest extends TestCase
{
    use RefreshDatabase;

    public function test_can_delete_currency_by_id_e2e(): void
    {
        $currency = Currency::factory()->create([
            'name' => 'Japanese Yen',
            'symbol' => '¥',
            'exchange_rate' => 155.0000,
        ]);

        $response = $this->deleteJson("/api/currencies/{$currency->id}");

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'application/json')
            ->assertJson([
                'message' => 'Divisa eliminada exitosamente.',
            ]);

        $this->assertDatabaseMissing('currencies', [
            'id' => $currency->id,
        ]);
    }

    public function test_delete_currency_cascades_to_products_and_prices_e2e(): void
    {
        $currency = Currency::factory()->create();
        $product = Product::factory()->create(['currency_id' => $currency->id]);
        $price = ProductPrice::factory()->create([
            'product_id' => $product->id,
            'currency_id' => $currency->id,
        ]);

        $this->assertDatabaseHas('currencies', ['id' => $currency->id]);
        $this->assertDatabaseHas('products', ['id' => $product->id]);
        $this->assertDatabaseHas('product_prices', ['id' => $price->id]);

        $response = $this->deleteJson("/api/currencies/{$currency->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Divisa eliminada exitosamente.',
            ]);

        $this->assertDatabaseMissing('currencies', ['id' => $currency->id]);
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
        $this->assertDatabaseMissing('product_prices', ['id' => $price->id]);
    }

    public function test_returns_404_with_custom_exception_message_when_currency_does_not_exist_e2e(): void
    {
        $response = $this->deleteJson('/api/currencies/99999');

        $response->assertStatus(404)
            ->assertHeader('Content-Type', 'application/json')
            ->assertJson([
                'message' => 'La divisa solicitada no fue encontrada.',
            ]);
    }

    public function test_non_numeric_id_returns_404_on_delete_e2e(): void
    {
        $response = $this->deleteJson('/api/currencies/abc');

        $response->assertStatus(404);
    }
}

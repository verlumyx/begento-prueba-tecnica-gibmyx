<?php

namespace Tests\Feature;

use App\Models\Currency;
use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelRelationshipTest extends TestCase
{
    use RefreshDatabase;

    public function test_currency_product_and_product_price_creation_and_relations(): void
    {
        // 1. Create Currency
        $usd = Currency::create([
            'name' => 'US Dollar',
            'symbol' => '$',
            'exchange_rate' => 1.0000,
        ]);

        $eur = Currency::create([
            'name' => 'Euro',
            'symbol' => '€',
            'exchange_rate' => 0.9200,
        ]);

        $this->assertDatabaseHas('currencies', ['symbol' => '$']);
        $this->assertDatabaseHas('currencies', ['symbol' => '€']);

        // 2. Create Product
        $product = Product::create([
            'name' => 'Laptop Gamer Pro',
            'description' => 'Laptop de alto rendimiento',
            'price' => 1200.50,
            'currency_id' => $usd->id,
            'tax_cost' => 192.08,
            'manufacturing_cost' => 750.00,
        ]);

        $this->assertDatabaseHas('products', ['name' => 'Laptop Gamer Pro']);
        $this->assertEquals($usd->id, $product->currency->id);
        $this->assertTrue($usd->products->contains($product));

        // 3. Create ProductPrice
        $productPrice = ProductPrice::create([
            'product_id' => $product->id,
            'currency_id' => $eur->id,
            'price' => 1104.46,
        ]);

        $this->assertDatabaseHas('product_prices', ['id' => $productPrice->id]);
        $this->assertEquals($product->id, $productPrice->product->id);
        $this->assertEquals($eur->id, $productPrice->currency->id);
        $this->assertTrue($product->prices->contains($productPrice));
        $this->assertTrue($eur->productPrices->contains($productPrice));
    }

    public function test_factories_work_properly(): void
    {
        $productPrice = ProductPrice::factory()->create();

        $this->assertNotNull($productPrice->id);
        $this->assertNotNull($productPrice->product);
        $this->assertNotNull($productPrice->currency);
        $this->assertNotNull($productPrice->product->currency);
    }

    public function test_cascade_delete(): void
    {
        $currency = Currency::factory()->create();
        $product = Product::factory()->create(['currency_id' => $currency->id]);
        $price = ProductPrice::factory()->create([
            'product_id' => $product->id,
            'currency_id' => $currency->id,
        ]);

        $this->assertDatabaseHas('products', ['id' => $product->id]);
        $this->assertDatabaseHas('product_prices', ['id' => $price->id]);

        $product->delete();

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
        $this->assertDatabaseMissing('product_prices', ['id' => $price->id]);
    }
}

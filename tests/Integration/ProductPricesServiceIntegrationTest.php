<?php

namespace Tests\Integration;

use App\Exceptions\ProductNotFoundException;
use App\Models\Currency;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Services\ProductPricesServices;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductPricesServiceIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_service_can_be_resolved_from_container(): void
    {
        $service = $this->app->make(ProductPricesServices::class);

        $this->assertInstanceOf(ProductPricesServices::class, $service);
    }

    public function test_service_can_create_price_for_existing_product_in_database(): void
    {
        $currency = Currency::factory()->create();
        $product = Product::factory()->create(['currency_id' => $currency->id]);

        /** @var ProductPricesServices $service */
        $service = $this->app->make(ProductPricesServices::class);

        $priceData = [
            'currency_id' => $currency->id,
            'price' => 1104.46,
        ];

        $result = $service->create($product->id, $priceData);

        $this->assertInstanceOf(ProductPrice::class, $result);
        $this->assertEquals($product->id, $result->product_id);
        $this->assertEquals($currency->id, $result->currency_id);
        $this->assertEquals(1104.46, $result->price);

        $this->assertDatabaseHas('product_prices', [
            'id' => $result->id,
            'product_id' => $product->id,
            'currency_id' => $currency->id,
            'price' => 1104.46,
        ]);
    }

    public function test_service_create_throws_exception_when_product_does_not_exist(): void
    {
        $this->expectException(ProductNotFoundException::class);
        $this->expectExceptionMessage('El producto solicitado no fue encontrado.');

        $currency = Currency::factory()->create();

        /** @var ProductPricesServices $service */
        $service = $this->app->make(ProductPricesServices::class);

        $service->create(99999, [
            'currency_id' => $currency->id,
            'price' => 100.00,
        ]);
    }

    public function test_service_can_find_prices_by_product_id_from_database(): void
    {
        $currency = Currency::factory()->create();
        $product = Product::factory()->create(['currency_id' => $currency->id]);

        ProductPrice::factory()->create([
            'product_id' => $product->id,
            'currency_id' => $currency->id,
            'price' => 99.99,
        ]);

        /** @var ProductPricesServices $service */
        $service = $this->app->make(ProductPricesServices::class);

        $prices = $service->findByProductId($product->id);

        $this->assertCount(1, $prices);
        $this->assertEquals(99.99, $prices->first()->price);
    }

    public function test_service_find_by_product_id_throws_exception_when_product_does_not_exist(): void
    {
        $this->expectException(ProductNotFoundException::class);
        $this->expectExceptionMessage('El producto solicitado no fue encontrado.');

        /** @var ProductPricesServices $service */
        $service = $this->app->make(ProductPricesServices::class);

        $service->findByProductId(99999);
    }
}

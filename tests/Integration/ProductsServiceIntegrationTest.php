<?php

namespace Tests\Integration;

use App\Models\Currency;
use App\Models\Product;
use App\Services\ProductsServices;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductsServiceIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_service_can_be_resolved_from_container_and_creates_product_in_database(): void
    {
        $currency = Currency::factory()->create([
            'name' => 'US Dollar',
            'symbol' => '$',
            'exchange_rate' => 1.0000,
        ]);

        /** @var ProductsServices $service */
        $service = $this->app->make(ProductsServices::class);

        $productData = [
            'name' => 'Wireless Gaming Mouse',
            'description' => 'Low latency sensor',
            'price' => 79.99,
            'currency_id' => $currency->id,
            'tax_cost' => 12.80,
            'manufacturing_cost' => 30.00,
        ];

        $result = $service->create($productData);

        $this->assertInstanceOf(Product::class, $result);
        $this->assertNotNull($result->id);
        $this->assertEquals('Wireless Gaming Mouse', $result->name);
        $this->assertEquals($currency->id, $result->currency_id);

        $this->assertDatabaseHas('products', [
            'id' => $result->id,
            'name' => 'Wireless Gaming Mouse',
            'currency_id' => $currency->id,
        ]);
    }

    public function test_service_can_cursor_paginate_real_data_from_database(): void
    {
        $currency = Currency::factory()->create();
        Product::factory()->count(4)->create(['currency_id' => $currency->id]);

        /** @var ProductsServices $service */
        $service = $this->app->make(ProductsServices::class);

        $result = $service->cursorPaginate(2);

        $this->assertCount(2, $result->items());
        $this->assertTrue($result->hasMorePages());
        $this->assertNotNull($result->nextCursor());
    }

    public function test_service_cursor_paginate_returns_empty_when_no_records(): void
    {
        /** @var ProductsServices $service */
        $service = $this->app->make(ProductsServices::class);

        $result = $service->cursorPaginate(15);

        $this->assertTrue($result->isEmpty());
    }
}

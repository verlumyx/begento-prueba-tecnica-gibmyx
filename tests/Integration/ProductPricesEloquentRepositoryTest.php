<?php

namespace Tests\Integration;

use App\Models\Currency;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Repositories\Contracts\ProductPricesRepository;
use App\Repositories\ProductPricesEloquentRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductPricesEloquentRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private ProductPricesEloquentRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new ProductPricesEloquentRepository;
    }

    public function test_repository_is_bound_in_service_container(): void
    {
        $resolved = $this->app->make(ProductPricesRepository::class);

        $this->assertInstanceOf(ProductPricesEloquentRepository::class, $resolved);
    }

    public function test_create_stores_product_price_in_database_and_returns_model(): void
    {
        $currency = Currency::factory()->create();
        $product = Product::factory()->create(['currency_id' => $currency->id]);

        $priceData = [
            'product_id' => $product->id,
            'currency_id' => $currency->id,
            'price' => 1104.46,
        ];

        $productPrice = $this->repository->create($priceData);

        $this->assertInstanceOf(ProductPrice::class, $productPrice);
        $this->assertDatabaseHas('product_prices', [
            'id' => $productPrice->id,
            'product_id' => $product->id,
            'currency_id' => $currency->id,
            'price' => 1104.46,
        ]);
    }

    public function test_find_by_product_id_returns_all_prices_for_given_product(): void
    {
        $currency1 = Currency::factory()->create();
        $currency2 = Currency::factory()->create();

        $product1 = Product::factory()->create(['currency_id' => $currency1->id]);
        $product2 = Product::factory()->create(['currency_id' => $currency1->id]);

        $price1 = ProductPrice::factory()->create([
            'product_id' => $product1->id,
            'currency_id' => $currency1->id,
            'price' => 100.00,
        ]);
        $price2 = ProductPrice::factory()->create([
            'product_id' => $product1->id,
            'currency_id' => $currency2->id,
            'price' => 92.00,
        ]);

        // Price for other product
        ProductPrice::factory()->create([
            'product_id' => $product2->id,
            'currency_id' => $currency1->id,
            'price' => 50.00,
        ]);

        $results = $this->repository->findByProductId($product1->id);

        $this->assertCount(2, $results);
        $this->assertTrue($results->pluck('id')->contains($price1->id));
        $this->assertTrue($results->pluck('id')->contains($price2->id));
    }

    public function test_find_by_product_id_returns_empty_collection_when_no_prices_exist(): void
    {
        $currency = Currency::factory()->create();
        $product = Product::factory()->create(['currency_id' => $currency->id]);

        $results = $this->repository->findByProductId($product->id);

        $this->assertCount(0, $results);
        $this->assertTrue($results->isEmpty());
    }
}

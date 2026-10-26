<?php

namespace Tests\Integration;

use App\Models\Currency;
use App\Models\Product;
use App\Repositories\Contracts\ProductsRepository;
use App\Repositories\ProductsEloquentRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductsEloquentRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private ProductsEloquentRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new ProductsEloquentRepository;
    }

    public function test_repository_is_bound_in_service_container(): void
    {
        $resolved = $this->app->make(ProductsRepository::class);

        $this->assertInstanceOf(ProductsEloquentRepository::class, $resolved);
    }

    public function test_create_stores_product_in_database_and_returns_model(): void
    {
        $currency = Currency::factory()->create([
            'name' => 'US Dollar',
            'symbol' => '$',
            'exchange_rate' => 1.0000,
        ]);

        $productData = [
            'name' => 'Laptop Gamer Pro',
            'description' => 'Laptop de alto rendimiento',
            'price' => 1200.50,
            'currency_id' => $currency->id,
            'tax_cost' => 192.08,
            'manufacturing_cost' => 750.00,
        ];

        $product = $this->repository->create($productData);

        $this->assertInstanceOf(Product::class, $product);
        $this->assertNotNull($product->id);
        $this->assertEquals('Laptop Gamer Pro', $product->name);
        $this->assertEquals('Laptop de alto rendimiento', $product->description);
        $this->assertEquals('1200.50', $product->price);
        $this->assertEquals($currency->id, $product->currency_id);
        $this->assertEquals('192.08', $product->tax_cost);
        $this->assertEquals('750.00', $product->manufacturing_cost);
        $this->assertNotNull($product->created_at);
        $this->assertNotNull($product->updated_at);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Laptop Gamer Pro',
            'currency_id' => $currency->id,
        ]);
    }

    public function test_create_stores_product_with_nullable_description(): void
    {
        $currency = Currency::factory()->create();

        $productData = [
            'name' => 'Mechanical Keyboard',
            'description' => null,
            'price' => 89.99,
            'currency_id' => $currency->id,
            'tax_cost' => 14.40,
            'manufacturing_cost' => 35.00,
        ];

        $product = $this->repository->create($productData);

        $this->assertInstanceOf(Product::class, $product);
        $this->assertNull($product->description);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'description' => null,
        ]);
    }

    public function test_created_product_belongs_to_currency_relation(): void
    {
        $currency = Currency::factory()->create([
            'name' => 'Euro',
            'symbol' => '€',
            'exchange_rate' => 0.9200,
        ]);

        $product = $this->repository->create([
            'name' => 'Monitor 4K',
            'description' => 'Ultra HD',
            'price' => 450.00,
            'currency_id' => $currency->id,
            'tax_cost' => 72.00,
            'manufacturing_cost' => 200.00,
        ]);

        $this->assertInstanceOf(Currency::class, $product->currency);
        $this->assertEquals($currency->id, $product->currency->id);
        $this->assertEquals('Euro', $product->currency->name);
    }

    public function test_cursor_paginate_returns_empty_when_database_is_empty(): void
    {
        $paginator = $this->repository->cursorPaginate(15);

        $this->assertTrue($paginator->isEmpty());
        $this->assertCount(0, $paginator->items());
    }

    public function test_cursor_paginate_returns_items_up_to_per_page(): void
    {
        $currency = Currency::factory()->create();
        Product::factory()->count(5)->create(['currency_id' => $currency->id]);

        $paginator = $this->repository->cursorPaginate(3);

        $this->assertCount(3, $paginator->items());
        $this->assertTrue($paginator->hasMorePages());
        $this->assertNotNull($paginator->nextCursor());
    }

    public function test_cursor_paginate_navigates_correctly_between_pages(): void
    {
        $currency = Currency::factory()->create();
        $p1 = Product::factory()->create(['currency_id' => $currency->id, 'name' => 'Item 1']);
        $p2 = Product::factory()->create(['currency_id' => $currency->id, 'name' => 'Item 2']);
        $p3 = Product::factory()->create(['currency_id' => $currency->id, 'name' => 'Item 3']);

        $page1 = $this->repository->cursorPaginate(2);
        $this->assertCount(2, $page1->items());
        $this->assertEquals($p1->id, $page1->items()[0]->id);
        $this->assertEquals($p2->id, $page1->items()[1]->id);

        $cursor = $page1->nextCursor();
        $this->assertNotNull($cursor);

        // Fetch second page passing the cursor through request context
        request()->merge(['cursor' => $cursor->encode()]);
        $page2 = $this->repository->cursorPaginate(2);

        $this->assertCount(1, $page2->items());
        $this->assertEquals($p3->id, $page2->items()[0]->id);
        $this->assertNull($page2->nextCursor());
    }

    public function test_find_by_id_returns_product_when_exists(): void
    {
        $currency = Currency::factory()->create();
        $created = Product::factory()->create([
            'currency_id' => $currency->id,
            'name' => 'Tablet Pro',
        ]);

        $found = $this->repository->findById($created->id);

        $this->assertInstanceOf(Product::class, $found);
        $this->assertEquals($created->id, $found->id);
        $this->assertEquals('Tablet Pro', $found->name);
    }

    public function test_find_by_id_returns_null_when_does_not_exist(): void
    {
        $found = $this->repository->findById(99999);

        $this->assertNull($found);
    }
}

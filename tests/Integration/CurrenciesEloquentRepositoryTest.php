<?php

namespace Tests\Integration;

use App\Models\Currency;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Repositories\Contracts\CurrenciesRepository;
use App\Repositories\CurrenciesEloquentRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class CurrenciesEloquentRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private CurrenciesEloquentRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new CurrenciesEloquentRepository;
    }

    public function test_repository_is_bound_in_service_container(): void
    {
        $resolved = $this->app->make(CurrenciesRepository::class);

        $this->assertInstanceOf(CurrenciesEloquentRepository::class, $resolved);
    }

    public function test_all_returns_empty_collection_when_database_is_empty(): void
    {
        $result = $this->repository->all();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertTrue($result->isEmpty());
        $this->assertCount(0, $result);
    }

    public function test_all_retrieves_all_currencies_from_database(): void
    {
        Currency::factory()->create([
            'name' => 'US Dollar',
            'symbol' => '$',
            'exchange_rate' => 1.0000,
        ]);

        Currency::factory()->create([
            'name' => 'Euro',
            'symbol' => '€',
            'exchange_rate' => 0.9200,
        ]);

        Currency::factory()->create([
            'name' => 'British Pound',
            'symbol' => '£',
            'exchange_rate' => 0.7900,
        ]);

        $result = $this->repository->all();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(3, $result);
        $this->assertTrue($result->contains('name', 'US Dollar'));
        $this->assertTrue($result->contains('name', 'Euro'));
        $this->assertTrue($result->contains('name', 'British Pound'));
    }

    public function test_all_returns_models_with_correct_attributes_and_casting(): void
    {
        $currency = Currency::factory()->create([
            'name' => 'Japanese Yen',
            'symbol' => '¥',
            'exchange_rate' => 155.2500,
        ]);

        $result = $this->repository->all();
        $first = $result->first();

        $this->assertInstanceOf(Currency::class, $first);
        $this->assertEquals($currency->id, $first->id);
        $this->assertEquals('Japanese Yen', $first->name);
        $this->assertEquals('¥', $first->symbol);
        $this->assertEquals('155.2500', $first->exchange_rate);
    }

    public function test_create_stores_currency_in_database_and_returns_model(): void
    {
        $data = [
            'name' => 'Canadian Dollar',
            'symbol' => 'CAD$',
            'exchange_rate' => 1.3500,
        ];

        $currency = $this->repository->create($data);

        $this->assertInstanceOf(Currency::class, $currency);
        $this->assertDatabaseHas('currencies', [
            'id' => $currency->id,
            'name' => 'Canadian Dollar',
            'symbol' => 'CAD$',
        ]);
    }

    public function test_find_by_id_returns_currency_when_exists(): void
    {
        $created = Currency::factory()->create([
            'name' => 'Swiss Franc',
            'symbol' => 'CHF',
            'exchange_rate' => 0.8800,
        ]);

        $found = $this->repository->findById($created->id);

        $this->assertInstanceOf(Currency::class, $found);
        $this->assertEquals($created->id, $found->id);
        $this->assertEquals('Swiss Franc', $found->name);
    }

    public function test_find_by_id_returns_null_when_does_not_exist(): void
    {
        $found = $this->repository->findById(99999);

        $this->assertNull($found);
    }

    public function test_update_modifies_currency_and_returns_fresh_instance(): void
    {
        $created = Currency::factory()->create([
            'name' => 'Old Currency',
            'symbol' => 'OC',
            'exchange_rate' => 1.0000,
        ]);

        $updated = $this->repository->update($created->id, [
            'name' => 'Updated Currency',
            'exchange_rate' => 1.2500,
        ]);

        $this->assertInstanceOf(Currency::class, $updated);
        $this->assertEquals('Updated Currency', $updated->name);
        $this->assertEquals('1.2500', $updated->exchange_rate);
        $this->assertDatabaseHas('currencies', [
            'id' => $created->id,
            'name' => 'Updated Currency',
        ]);
    }

    public function test_update_returns_null_when_currency_does_not_exist(): void
    {
        $result = $this->repository->update(99999, ['name' => 'None']);

        $this->assertNull($result);
    }

    public function test_delete_removes_currency_and_returns_true(): void
    {
        $currency = Currency::factory()->create();

        $this->assertDatabaseHas('currencies', ['id' => $currency->id]);

        $result = $this->repository->delete($currency->id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('currencies', ['id' => $currency->id]);
    }

    public function test_delete_returns_false_when_currency_does_not_exist(): void
    {
        $result = $this->repository->delete(99999);

        $this->assertFalse($result);
    }

    public function test_delete_cascades_to_products_and_prices(): void
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

        $result = $this->repository->delete($currency->id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('currencies', ['id' => $currency->id]);
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
        $this->assertDatabaseMissing('product_prices', ['id' => $price->id]);
    }
}

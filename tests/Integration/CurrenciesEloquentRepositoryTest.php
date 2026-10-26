<?php

namespace Tests\Integration;

use App\Models\Currency;
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
        $this->repository = new CurrenciesEloquentRepository();
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
}

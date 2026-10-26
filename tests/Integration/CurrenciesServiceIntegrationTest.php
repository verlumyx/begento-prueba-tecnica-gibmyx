<?php

namespace Tests\Integration;

use App\Exceptions\CurrencyNotFoundException;
use App\Models\Currency;
use App\Services\CurrenciesServices;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class CurrenciesServiceIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_service_can_be_resolved_from_container_and_fetches_real_data(): void
    {
        Currency::factory()->count(3)->create();

        /** @var CurrenciesServices $service */
        $service = $this->app->make(CurrenciesServices::class);

        $result = $service->all();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(3, $result);
        $this->assertInstanceOf(Currency::class, $result->first());
    }

    public function test_service_returns_empty_collection_when_no_records(): void
    {
        /** @var CurrenciesServices $service */
        $service = $this->app->make(CurrenciesServices::class);

        $result = $service->all();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertTrue($result->isEmpty());
    }

    public function test_service_can_create_currency_in_database(): void
    {
        /** @var CurrenciesServices $service */
        $service = $this->app->make(CurrenciesServices::class);

        $currency = $service->create([
            'name' => 'Australian Dollar',
            'symbol' => 'AUD$',
            'exchange_rate' => 1.5200,
        ]);

        $this->assertInstanceOf(Currency::class, $currency);
        $this->assertDatabaseHas('currencies', [
            'id' => $currency->id,
            'name' => 'Australian Dollar',
        ]);
    }

    public function test_service_can_find_currency_by_id(): void
    {
        $created = Currency::factory()->create(['name' => 'Swiss Franc']);

        /** @var CurrenciesServices $service */
        $service = $this->app->make(CurrenciesServices::class);

        $found = $service->findById($created->id);

        $this->assertInstanceOf(Currency::class, $found);
        $this->assertEquals($created->id, $found->id);
        $this->assertEquals('Swiss Franc', $found->name);
    }

    public function test_service_find_by_id_throws_exception_when_not_found(): void
    {
        $this->expectException(CurrencyNotFoundException::class);
        $this->expectExceptionMessage('La divisa solicitada no fue encontrada.');

        /** @var CurrenciesServices $service */
        $service = $this->app->make(CurrenciesServices::class);

        $service->findById(99999);
    }

    public function test_service_can_update_currency_in_database(): void
    {
        $created = Currency::factory()->create(['name' => 'Old Currency']);

        /** @var CurrenciesServices $service */
        $service = $this->app->make(CurrenciesServices::class);

        $updated = $service->update($created->id, [
            'name' => 'New Currency',
        ]);

        $this->assertInstanceOf(Currency::class, $updated);
        $this->assertEquals('New Currency', $updated->name);
        $this->assertDatabaseHas('currencies', [
            'id' => $created->id,
            'name' => 'New Currency',
        ]);
    }

    public function test_service_update_throws_exception_when_currency_not_found(): void
    {
        $this->expectException(CurrencyNotFoundException::class);
        $this->expectExceptionMessage('La divisa solicitada no fue encontrada.');

        /** @var CurrenciesServices $service */
        $service = $this->app->make(CurrenciesServices::class);

        $service->update(99999, ['name' => 'None']);
    }

    public function test_service_can_delete_currency_from_database(): void
    {
        $created = Currency::factory()->create();

        /** @var CurrenciesServices $service */
        $service = $this->app->make(CurrenciesServices::class);

        $result = $service->delete($created->id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('currencies', ['id' => $created->id]);
    }

    public function test_service_delete_throws_exception_when_currency_not_found(): void
    {
        $this->expectException(CurrencyNotFoundException::class);
        $this->expectExceptionMessage('La divisa solicitada no fue encontrada.');

        /** @var CurrenciesServices $service */
        $service = $this->app->make(CurrenciesServices::class);

        $service->delete(99999);
    }
}

<?php

namespace Tests\Integration;

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
}

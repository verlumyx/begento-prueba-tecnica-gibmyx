<?php

namespace Tests\Unit;

use App\Repositories\Contracts\CurrenciesRepository;
use App\Services\CurrenciesServices;
use Illuminate\Support\Collection;
use Mockery;
use Tests\TestCase;

class CurrenciesServicesTest extends TestCase
{
    public function test_all_returns_currencies_collection_from_repository(): void
    {
        $expectedCollection = collect([
            [
                'id' => 1,
                'name' => 'US Dollar',
                'symbol' => '$',
                'exchange_rate' => '1.0000',
            ],
            [
                'id' => 2,
                'name' => 'Euro',
                'symbol' => '€',
                'exchange_rate' => '0.9200',
            ],
        ]);

        $mockRepository = Mockery::mock(CurrenciesRepository::class);
        $mockRepository->shouldReceive('all')
            ->once()
            ->andReturn($expectedCollection);

        $service = new CurrenciesServices($mockRepository);
        $result = $service->all();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(2, $result);
        $this->assertEquals($expectedCollection, $result);
    }

    public function test_all_returns_empty_collection_when_repository_is_empty(): void
    {
        $mockRepository = Mockery::mock(CurrenciesRepository::class);
        $mockRepository->shouldReceive('all')
            ->once()
            ->andReturn(collect([]));

        $service = new CurrenciesServices($mockRepository);
        $result = $service->all();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertTrue($result->isEmpty());
    }
}

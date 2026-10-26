<?php

namespace Tests\Unit;

use App\Exceptions\CurrencyNotFoundException;
use App\Models\Currency;
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

    public function test_create_calls_repository_and_returns_created_currency(): void
    {
        $data = [
            'name' => 'US Dollar',
            'symbol' => '$',
            'exchange_rate' => 1.0000,
        ];

        $expectedCurrency = new Currency($data);
        $expectedCurrency->id = 1;

        $mockRepository = Mockery::mock(CurrenciesRepository::class);
        $mockRepository->shouldReceive('create')
            ->once()
            ->with($data)
            ->andReturn($expectedCurrency);

        $service = new CurrenciesServices($mockRepository);
        $result = $service->create($data);

        $this->assertInstanceOf(Currency::class, $result);
        $this->assertEquals(1, $result->id);
        $this->assertEquals('US Dollar', $result->name);
    }

    public function test_find_by_id_returns_currency_when_found(): void
    {
        $currency = new Currency(['name' => 'Euro', 'symbol' => '€', 'exchange_rate' => 0.9200]);
        $currency->id = 2;

        $mockRepository = Mockery::mock(CurrenciesRepository::class);
        $mockRepository->shouldReceive('findById')
            ->once()
            ->with(2)
            ->andReturn($currency);

        $service = new CurrenciesServices($mockRepository);
        $result = $service->findById(2);

        $this->assertInstanceOf(Currency::class, $result);
        $this->assertEquals(2, $result->id);
        $this->assertEquals('Euro', $result->name);
    }

    public function test_find_by_id_throws_currency_not_found_exception_when_not_found(): void
    {
        $this->expectException(CurrencyNotFoundException::class);
        $this->expectExceptionMessage('La divisa solicitada no fue encontrada.');

        $mockRepository = Mockery::mock(CurrenciesRepository::class);
        $mockRepository->shouldReceive('findById')
            ->once()
            ->with(999)
            ->andReturn(null);

        $service = new CurrenciesServices($mockRepository);
        $service->findById(999);
    }

    public function test_update_calls_repository_and_returns_updated_currency(): void
    {
        $existing = new Currency(['name' => 'Old Name']);
        $existing->id = 1;

        $updateData = ['name' => 'New Name'];
        $updatedCurrency = new Currency(['name' => 'New Name']);
        $updatedCurrency->id = 1;

        $mockRepository = Mockery::mock(CurrenciesRepository::class);
        $mockRepository->shouldReceive('findById')
            ->once()
            ->with(1)
            ->andReturn($existing);

        $mockRepository->shouldReceive('update')
            ->once()
            ->with(1, $updateData)
            ->andReturn($updatedCurrency);

        $service = new CurrenciesServices($mockRepository);
        $result = $service->update(1, $updateData);

        $this->assertInstanceOf(Currency::class, $result);
        $this->assertEquals('New Name', $result->name);
    }

    public function test_update_throws_currency_not_found_exception_when_currency_does_not_exist(): void
    {
        $this->expectException(CurrencyNotFoundException::class);
        $this->expectExceptionMessage('La divisa solicitada no fue encontrada.');

        $mockRepository = Mockery::mock(CurrenciesRepository::class);
        $mockRepository->shouldReceive('findById')
            ->once()
            ->with(999)
            ->andReturn(null);

        $service = new CurrenciesServices($mockRepository);
        $service->update(999, ['name' => 'New']);
    }

    public function test_delete_calls_repository_and_returns_true_when_successful(): void
    {
        $mockRepository = Mockery::mock(CurrenciesRepository::class);
        $mockRepository->shouldReceive('delete')
            ->once()
            ->with(1)
            ->andReturn(true);

        $service = new CurrenciesServices($mockRepository);
        $result = $service->delete(1);

        $this->assertTrue($result);
    }

    public function test_delete_throws_currency_not_found_exception_when_not_found(): void
    {
        $this->expectException(CurrencyNotFoundException::class);
        $this->expectExceptionMessage('La divisa solicitada no fue encontrada.');

        $mockRepository = Mockery::mock(CurrenciesRepository::class);
        $mockRepository->shouldReceive('delete')
            ->once()
            ->with(999)
            ->andReturn(false);

        $service = new CurrenciesServices($mockRepository);
        $service->delete(999);
    }
}

<?php

namespace Tests\Feature;

use App\Models\Currency;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CurrenciesGetE2ETest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_all_currencies_e2e(): void
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

        $response = $this->getJson('/api/currencies');

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'application/json')
            ->assertJsonCount(2)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'name',
                    'symbol',
                    'exchange_rate',
                    'created_at',
                    'updated_at',
                ],
            ])
            ->assertJsonFragment([
                'name' => 'US Dollar',
                'symbol' => '$',
                'exchange_rate' => '1.0000',
            ])
            ->assertJsonFragment([
                'name' => 'Euro',
                'symbol' => '€',
                'exchange_rate' => '0.9200',
            ]);
    }

    public function test_returns_empty_array_when_no_currencies_exist_e2e(): void
    {
        $response = $this->getJson('/api/currencies');

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'application/json')
            ->assertExactJson([]);
    }

    public function test_currencies_endpoint_rejects_unsupported_methods(): void
    {
        $postResponse = $this->postJson('/api/currencies');
        $postResponse->assertStatus(405);

        $putResponse = $this->putJson('/api/currencies');
        $putResponse->assertStatus(405);

        $deleteResponse = $this->deleteJson('/api/currencies');
        $deleteResponse->assertStatus(405);
    }
}

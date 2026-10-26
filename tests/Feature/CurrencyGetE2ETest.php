<?php

namespace Tests\Feature;

use App\Models\Currency;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CurrencyGetE2ETest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_currency_by_id_e2e(): void
    {
        $currency = Currency::factory()->create([
            'name' => 'Euro',
            'symbol' => '€',
            'exchange_rate' => 0.9200,
        ]);

        $response = $this->getJson("/api/currencies/{$currency->id}");

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'application/json')
            ->assertJsonStructure([
                'id',
                'name',
                'symbol',
                'exchange_rate',
                'created_at',
                'updated_at',
            ])
            ->assertJson([
                'id' => $currency->id,
                'name' => 'Euro',
                'symbol' => '€',
                'exchange_rate' => '0.9200',
            ]);
    }

    public function test_returns_404_with_custom_exception_message_when_currency_does_not_exist_e2e(): void
    {
        $response = $this->getJson('/api/currencies/99999');

        $response->assertStatus(404)
            ->assertHeader('Content-Type', 'application/json')
            ->assertJson([
                'message' => 'La divisa solicitada no fue encontrada.',
            ]);
    }

    public function test_non_numeric_id_returns_404_e2e(): void
    {
        $response = $this->getJson('/api/currencies/abc');

        $response->assertStatus(404);
    }
}

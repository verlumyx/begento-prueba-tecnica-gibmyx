<?php

namespace Tests\Feature;

use App\Models\Currency;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CurrencyPutE2ETest extends TestCase
{
    use RefreshDatabase;

    public function test_can_update_currency_e2e(): void
    {
        $currency = Currency::factory()->create([
            'name' => 'British Pound',
            'symbol' => '£',
            'exchange_rate' => 0.7900,
        ]);

        $payload = [
            'name' => 'Pound Sterling',
            'symbol' => 'GBP',
            'exchange_rate' => 0.8100,
        ];

        $response = $this->putJson("/api/currencies/{$currency->id}", $payload);

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'application/json')
            ->assertJson([
                'id' => $currency->id,
                'name' => 'Pound Sterling',
                'symbol' => 'GBP',
                'exchange_rate' => '0.8100',
            ]);

        $this->assertDatabaseHas('currencies', [
            'id' => $currency->id,
            'name' => 'Pound Sterling',
            'symbol' => 'GBP',
        ]);
    }

    public function test_returns_404_when_updating_non_existent_currency_e2e(): void
    {
        $response = $this->putJson('/api/currencies/99999', [
            'name' => 'Non Existent',
        ]);

        $response->assertStatus(404)
            ->assertHeader('Content-Type', 'application/json')
            ->assertJson([
                'message' => 'La divisa solicitada no fue encontrada.',
            ]);
    }

    public function test_validation_error_when_invalid_data_on_update_e2e(): void
    {
        $currency = Currency::factory()->create();

        $response = $this->putJson("/api/currencies/{$currency->id}", [
            'symbol' => '12345678901',
            'exchange_rate' => -1,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['symbol', 'exchange_rate']);
    }

    public function test_non_numeric_id_returns_404_on_update_e2e(): void
    {
        $response = $this->putJson('/api/currencies/abc', [
            'name' => 'Test',
        ]);

        $response->assertStatus(404);
    }
}

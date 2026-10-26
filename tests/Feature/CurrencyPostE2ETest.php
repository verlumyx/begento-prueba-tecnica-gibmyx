<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CurrencyPostE2ETest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_currency_successfully_e2e(): void
    {
        $payload = [
            'name' => 'US Dollar',
            'symbol' => '$',
            'exchange_rate' => 1.0000,
        ];

        $response = $this->postJson('/api/currencies', $payload);

        $response->assertStatus(201)
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
                'name' => 'US Dollar',
                'symbol' => '$',
                'exchange_rate' => '1.0000',
            ]);

        $this->assertDatabaseHas('currencies', [
            'name' => 'US Dollar',
            'symbol' => '$',
        ]);
    }

    public function test_validation_errors_when_required_fields_are_missing_e2e(): void
    {
        $response = $this->postJson('/api/currencies', []);

        $response->assertStatus(422)
            ->assertHeader('Content-Type', 'application/json')
            ->assertJsonValidationErrors(['name', 'symbol', 'exchange_rate']);
    }

    public function test_validation_error_when_exchange_rate_is_invalid_e2e(): void
    {
        $payload = [
            'name' => 'Zero Rate Currency',
            'symbol' => 'ZRC',
            'exchange_rate' => 0,
        ];

        $response = $this->postJson('/api/currencies', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['exchange_rate']);
    }
}

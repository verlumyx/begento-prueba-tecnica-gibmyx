<?php

namespace Tests\Feature;

use App\Models\Currency;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductPostE2ETest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_product_successfully_e2e(): void
    {
        $currency = Currency::factory()->create([
            'name' => 'US Dollar',
            'symbol' => '$',
            'exchange_rate' => 1.0000,
        ]);

        $payload = [
            'name' => 'Laptop Gamer Asus ROG',
            'description' => 'Laptop de alto rendimiento para gaming y diseño 3D con procesador Ryzen 9 y RTX 4080.',
            'price' => 1899.99,
            'currency_id' => $currency->id,
            'tax_cost' => 303.99,
            'manufacturing_cost' => 1150.00,
        ];

        $response = $this->postJson('/api/products', $payload);

        $response->assertStatus(201)
            ->assertHeader('Content-Type', 'application/json')
            ->assertJsonStructure([
                'id',
                'name',
                'description',
                'price',
                'currency_id',
                'tax_cost',
                'manufacturing_cost',
                'created_at',
                'updated_at',
            ])
            ->assertJson([
                'name' => 'Laptop Gamer Asus ROG',
                'description' => 'Laptop de alto rendimiento para gaming y diseño 3D con procesador Ryzen 9 y RTX 4080.',
                'price' => '1899.99',
                'currency_id' => $currency->id,
                'tax_cost' => '303.99',
                'manufacturing_cost' => '1150.00',
            ]);

        $this->assertDatabaseHas('products', [
            'name' => 'Laptop Gamer Asus ROG',
            'currency_id' => $currency->id,
            'price' => 1899.99,
            'tax_cost' => 303.99,
            'manufacturing_cost' => 1150.00,
        ]);
    }

    public function test_can_create_product_with_nullable_description_e2e(): void
    {
        $currency = Currency::factory()->create();

        $payload = [
            'name' => 'Teclado Mecánico',
            'description' => null,
            'price' => 129.99,
            'currency_id' => $currency->id,
            'tax_cost' => 20.80,
            'manufacturing_cost' => 55.00,
        ];

        $response = $this->postJson('/api/products', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'name' => 'Teclado Mecánico',
                'description' => null,
                'price' => '129.99',
                'currency_id' => $currency->id,
            ]);

        $this->assertDatabaseHas('products', [
            'name' => 'Teclado Mecánico',
            'description' => null,
            'currency_id' => $currency->id,
        ]);
    }

    public function test_validation_errors_when_required_fields_are_missing_e2e(): void
    {
        $response = $this->postJson('/api/products', []);

        $response->assertStatus(422)
            ->assertHeader('Content-Type', 'application/json')
            ->assertJsonValidationErrors([
                'name',
                'price',
                'currency_id',
                'tax_cost',
                'manufacturing_cost',
            ]);
    }

    public function test_validation_error_when_currency_id_does_not_exist_e2e(): void
    {
        $payload = [
            'name' => 'Tablet Pro 11',
            'description' => 'Tablet con pantalla OLED',
            'price' => 799.00,
            'currency_id' => 999999,
            'tax_cost' => 127.84,
            'manufacturing_cost' => 450.00,
        ];

        $response = $this->postJson('/api/products', $payload);

        $response->assertStatus(422)
            ->assertHeader('Content-Type', 'application/json')
            ->assertJsonValidationErrors(['currency_id']);
    }

    public function test_validation_error_when_numeric_values_are_negative_e2e(): void
    {
        $currency = Currency::factory()->create();

        $payload = [
            'name' => 'Smartphone Ultra',
            'description' => 'Flagship smartphone',
            'price' => -100.00,
            'currency_id' => $currency->id,
            'tax_cost' => -10.00,
            'manufacturing_cost' => -50.00,
        ];

        $response = $this->postJson('/api/products', $payload);

        $response->assertStatus(422)
            ->assertHeader('Content-Type', 'application/json')
            ->assertJsonValidationErrors([
                'price',
                'tax_cost',
                'manufacturing_cost',
            ]);
    }
}

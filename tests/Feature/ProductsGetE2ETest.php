<?php

namespace Tests\Feature;

use App\Models\Currency;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductsGetE2ETest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_products_with_cursor_pagination_e2e(): void
    {
        $currency = Currency::factory()->create([
            'name' => 'US Dollar',
            'symbol' => '$',
            'exchange_rate' => 1.0000,
        ]);

        Product::factory()->create([
            'name' => 'Teclado Mecanico RGB',
            'description' => 'Switches red',
            'price' => 89.99,
            'currency_id' => $currency->id,
            'tax_cost' => 14.40,
            'manufacturing_cost' => 35.00,
        ]);

        Product::factory()->create([
            'name' => 'Mouse Optico Pro',
            'description' => 'Sensor 16000 DPI',
            'price' => 59.99,
            'currency_id' => $currency->id,
            'tax_cost' => 9.60,
            'manufacturing_cost' => 22.00,
        ]);

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'application/json')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'description',
                        'price',
                        'currency_id',
                        'tax_cost',
                        'manufacturing_cost',
                        'created_at',
                        'updated_at',
                    ],
                ],
                'path',
                'per_page',
                'next_cursor',
                'next_page_url',
                'prev_cursor',
                'prev_page_url',
            ])
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment(['name' => 'Teclado Mecanico RGB'])
            ->assertJsonFragment(['name' => 'Mouse Optico Pro']);
    }

    public function test_cursor_pagination_navigates_between_pages_e2e(): void
    {
        $currency = Currency::factory()->create();

        $p1 = Product::factory()->create(['currency_id' => $currency->id, 'name' => 'Producto 1']);
        $p2 = Product::factory()->create(['currency_id' => $currency->id, 'name' => 'Producto 2']);
        $p3 = Product::factory()->create(['currency_id' => $currency->id, 'name' => 'Producto 3']);
        $p4 = Product::factory()->create(['currency_id' => $currency->id, 'name' => 'Producto 4']);
        $p5 = Product::factory()->create(['currency_id' => $currency->id, 'name' => 'Producto 5']);

        // First page
        $page1Response = $this->getJson('/api/products?per_page=2');
        $page1Response->assertStatus(200)
            ->assertJsonCount(2, 'data');

        $page1Data = $page1Response->json('data');
        $this->assertEquals($p1->id, $page1Data[0]['id']);
        $this->assertEquals($p2->id, $page1Data[1]['id']);

        $nextCursor = $page1Response->json('next_cursor');
        $this->assertNotNull($nextCursor);

        // Second page
        $page2Response = $this->getJson('/api/products?per_page=2&cursor='.$nextCursor);
        $page2Response->assertStatus(200)
            ->assertJsonCount(2, 'data');

        $page2Data = $page2Response->json('data');
        $this->assertEquals($p3->id, $page2Data[0]['id']);
        $this->assertEquals($p4->id, $page2Data[1]['id']);

        $secondCursor = $page2Response->json('next_cursor');
        $this->assertNotNull($secondCursor);

        // Third page
        $page3Response = $this->getJson('/api/products?per_page=2&cursor='.$secondCursor);
        $page3Response->assertStatus(200)
            ->assertJsonCount(1, 'data');

        $page3Data = $page3Response->json('data');
        $this->assertEquals($p5->id, $page3Data[0]['id']);
        $this->assertNull($page3Response->json('next_cursor'));
    }

    public function test_returns_empty_data_when_no_products_exist_e2e(): void
    {
        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'application/json')
            ->assertJsonCount(0, 'data')
            ->assertJson([
                'data' => [],
                'next_cursor' => null,
                'prev_cursor' => null,
            ]);
    }

    public function test_respects_custom_per_page_e2e(): void
    {
        $currency = Currency::factory()->create();
        Product::factory()->count(10)->create(['currency_id' => $currency->id]);

        $response = $this->getJson('/api/products?per_page=4');

        $response->assertStatus(200)
            ->assertJson([
                'per_page' => 4,
            ])
            ->assertJsonCount(4, 'data');
    }
}

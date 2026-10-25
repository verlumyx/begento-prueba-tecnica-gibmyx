<?php

namespace Database\Seeders;

use App\Models\Currency;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure at least USD currency exists
        $baseCurrency = Currency::first() ?? Currency::create([
            'name' => 'US Dollar',
            'symbol' => '$',
            'exchange_rate' => 1.0000,
        ]);

        $products = [
            [
                'id' => 1,
                'name' => 'Laptop Gamer Asus ROG',
                'description' => 'Laptop de alto rendimiento para gaming y diseño 3D con procesador Ryzen 9 y RTX 4080.',
                'price' => 1899.99,
                'currency_id' => $baseCurrency->id,
                'tax_cost' => 303.99,
                'manufacturing_cost' => 1150.00,
            ],
            [
                'id' => 2,
                'name' => 'Monitor Curvo Samsung 34" UltraWide',
                'description' => 'Monitor WQHD 165Hz con tecnología Quantum Dot y soporte HDR.',
                'price' => 649.50,
                'currency_id' => $baseCurrency->id,
                'tax_cost' => 103.92,
                'manufacturing_cost' => 380.00,
            ],
            [
                'id' => 3,
                'name' => 'Teclado Mecánico Inalámbrico RGB',
                'description' => 'Switches mecánicos táctiles, conectividad Bluetooth 5.1 y chasis de aluminio.',
                'price' => 129.99,
                'currency_id' => $baseCurrency->id,
                'tax_cost' => 20.80,
                'manufacturing_cost' => 55.00,
            ],
            [
                'id' => 4,
                'name' => 'Mouse Ergonómico MX Master 3S',
                'description' => 'Sensor óptico de 8000 DPI, scroll electromagnético y clicks ultrasilenciosos.',
                'price' => 99.00,
                'currency_id' => $baseCurrency->id,
                'tax_cost' => 15.84,
                'manufacturing_cost' => 42.00,
            ],
            [
                'id' => 5,
                'name' => 'Auriculares Inalámbricos Noise Cancelling',
                'description' => 'Cancelación activa de ruido líder en la industria, audio de alta resolución y 30h de batería.',
                'price' => 349.99,
                'currency_id' => $baseCurrency->id,
                'tax_cost' => 56.00,
                'manufacturing_cost' => 160.00,
            ],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['id' => $product['id']],
                $product
            );
        }
    }
}

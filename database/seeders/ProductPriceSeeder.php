<?php

namespace Database\Seeders;

use App\Models\Currency;
use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Database\Seeder;

class ProductPriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Asegurar que existan monedas y productos antes de sembrar precios
        if (Currency::count() === 0) {
            $this->call(CurrencySeeder::class);
        }

        if (Product::count() === 0) {
            $this->call(ProductSeeder::class);
        }

        $products = Product::all();
        $currencies = Currency::all();

        foreach ($products as $product) {
            foreach ($currencies as $currency) {
                // Registrar precio en divisas secundarias (o todas las divisas)
                // Calculado según la tasa de cambio o un valor representativo
                $calculatedPrice = round($product->price * (float) $currency->exchange_rate, 2);

                ProductPrice::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'currency_id' => $currency->id,
                    ],
                    [
                        'price' => $calculatedPrice,
                    ]
                );
            }
        }
    }
}

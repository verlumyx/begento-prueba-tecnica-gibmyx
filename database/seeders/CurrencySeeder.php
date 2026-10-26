<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            [
                'id' => 1,
                'name' => 'US Dollar',
                'symbol' => '$',
                'exchange_rate' => 1.0000,
            ],
            [
                'id' => 2,
                'name' => 'Euro',
                'symbol' => '€',
                'exchange_rate' => 0.9200,
            ],
        ];

        foreach ($currencies as $currency) {
            Currency::updateOrCreate(
                ['id' => $currency['id']],
                $currency
            );
        }
    }
}

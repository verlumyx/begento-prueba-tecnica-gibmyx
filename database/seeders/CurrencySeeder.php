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
            [
                'id' => 3,
                'name' => 'British Pound',
                'symbol' => '£',
                'exchange_rate' => 0.7900,
            ],
            [
                'id' => 4,
                'name' => 'Mexican Peso',
                'symbol' => 'MX$',
                'exchange_rate' => 17.5000,
            ],
            [
                'id' => 5,
                'name' => 'Canadian Dollar',
                'symbol' => 'CA$',
                'exchange_rate' => 1.3600,
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

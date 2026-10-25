<?php

namespace Database\Factories;

use App\Models\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Currency>
 */
class CurrencyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['US Dollar', 'Euro', 'British Pound', 'Japanese Yen', 'Mexican Peso']),
            'symbol' => fake()->randomElement(['$', '€', '£', '¥', 'MX$']),
            'exchange_rate' => fake()->randomFloat(4, 0.5, 100),
        ];
    }
}

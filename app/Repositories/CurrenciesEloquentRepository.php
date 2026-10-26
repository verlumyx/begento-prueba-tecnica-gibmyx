<?php

namespace App\Repositories;

use App\Models\Currency;
use App\Repositories\Contracts\CurrenciesRepository;
use Illuminate\Support\Collection;

class CurrenciesEloquentRepository implements CurrenciesRepository
{
    /**
     * Get all currencies.
     */
    public function all(): Collection
    {
        return Currency::get();
    }

    /**
     * Create a new currency.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Currency
    {
        return Currency::create($data);
    }

    /**
     * Find a currency by its ID.
     */
    public function findById(int $id): ?Currency
    {
        return Currency::find($id);
    }

    /**
     * Update a currency by its ID.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(int $id, array $data): ?Currency
    {
        $currency = $this->findById($id);

        if (! $currency) {
            return null;
        }

        $currency->update($data);

        return $currency->fresh();
    }

    /**
     * Delete a currency by its ID.
     */
    public function delete(int $id): bool
    {
        $currency = $this->findById($id);

        if (! $currency) {
            return false;
        }

        return (bool) $currency->delete();
    }
}

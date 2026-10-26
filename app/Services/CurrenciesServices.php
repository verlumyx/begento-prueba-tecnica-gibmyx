<?php

namespace App\Services;

use App\Exceptions\CurrencyNotFoundException;
use App\Models\Currency;
use App\Repositories\Contracts\CurrenciesRepository;
use Illuminate\Support\Collection;

class CurrenciesServices
{
    public function __construct(private CurrenciesRepository $currenciesRepository) {}

    /**
     * Get all currencies.
     */
    public function all(): Collection
    {
        return $this->currenciesRepository->all();
    }

    /**
     * Create a new currency.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Currency
    {
        return $this->currenciesRepository->create($data);
    }

    /**
     * Find a currency by its ID or throw a CurrencyNotFoundException.
     *
     * @throws CurrencyNotFoundException
     */
    public function findById(int $id): Currency
    {
        $currency = $this->currenciesRepository->findById($id);

        if (! $currency) {
            throw new CurrencyNotFoundException;
        }

        return $currency;
    }

    /**
     * Update a currency by its ID or throw a CurrencyNotFoundException.
     *
     * @param  array<string, mixed>  $data
     *
     * @throws CurrencyNotFoundException
     */
    public function update(int $id, array $data): Currency
    {
        $this->findById($id);

        $updated = $this->currenciesRepository->update($id, $data);

        if (! $updated) {
            throw new CurrencyNotFoundException;
        }

        return $updated;
    }

    /**
     * Delete a currency by its ID or throw a CurrencyNotFoundException.
     *
     * @throws CurrencyNotFoundException
     */
    public function delete(int $id): bool
    {
        $deleted = $this->currenciesRepository->delete($id);

        if (! $deleted) {
            throw new CurrencyNotFoundException;
        }

        return true;
    }
}

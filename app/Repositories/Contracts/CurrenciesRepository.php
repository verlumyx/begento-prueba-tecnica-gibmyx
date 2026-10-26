<?php

namespace App\Repositories\Contracts;

use App\Models\Currency;
use Illuminate\Support\Collection;

interface CurrenciesRepository
{
    /**
     * Get all currencies.
     */
    public function all(): Collection;

    /**
     * Create a new currency.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Currency;

    /**
     * Find a currency by its ID.
     */
    public function findById(int $id): ?Currency;

    /**
     * Update a currency by its ID.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(int $id, array $data): ?Currency;

    /**
     * Delete a currency by its ID.
     */
    public function delete(int $id): bool;
}

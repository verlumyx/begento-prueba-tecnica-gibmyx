<?php

namespace App\Repositories\Contracts;

use App\Models\Product;
use Illuminate\Contracts\Pagination\CursorPaginator;

interface ProductsRepository
{
    /**
     * Create a new product.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Product;

    /**
     * Paginate products using cursor pagination.
     */
    public function cursorPaginate(int $perPage = 15): CursorPaginator;

    /**
     * Find a product by its ID.
     */
    public function findById(int $id): ?Product;

    /**
     * Update a product by its ID.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(int $id, array $data): ?Product;

    /**
     * Delete a product by its ID.
     */
    public function delete(int $id): bool;
}

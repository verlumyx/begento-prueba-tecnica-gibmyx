<?php

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\Contracts\ProductsRepository;
use Illuminate\Contracts\Pagination\CursorPaginator;

class ProductsEloquentRepository implements ProductsRepository
{
    /**
     * Create a new product.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Product
    {
        return Product::create($data);
    }

    /**
     * Paginate products using cursor pagination.
     */
    public function cursorPaginate(int $perPage = 15): CursorPaginator
    {
        return Product::cursorPaginate($perPage);
    }

    /**
     * Find a product by its ID.
     */
    public function findById(int $id): ?Product
    {
        return Product::find($id);
    }
}

<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\Contracts\ProductsRepository;
use Illuminate\Contracts\Pagination\CursorPaginator;

class ProductsServices
{
    public function __construct(private ProductsRepository $productsRepository) {}

    /**
     * Create a new product.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Product
    {
        return $this->productsRepository->create($data);
    }

    /**
     * Paginate products using cursor pagination.
     */
    public function cursorPaginate(int $perPage = 15): CursorPaginator
    {
        return $this->productsRepository->cursorPaginate($perPage);
    }
}

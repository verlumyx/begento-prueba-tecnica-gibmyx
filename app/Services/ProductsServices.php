<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\Contracts\ProductsRepository;

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
}

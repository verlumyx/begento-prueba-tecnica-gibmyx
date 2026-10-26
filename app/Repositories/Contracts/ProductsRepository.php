<?php

namespace App\Repositories\Contracts;

use App\Models\Product;

interface ProductsRepository
{
    /**
     * Create a new product.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Product;
}

<?php

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\Contracts\ProductsRepository;

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
}

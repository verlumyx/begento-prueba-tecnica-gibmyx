<?php

namespace App\Repositories\Contracts;

use App\Models\ProductPrice;
use Illuminate\Database\Eloquent\Collection;

interface ProductPricesRepository
{
    /**
     * Get all prices for a given product ID.
     *
     * @return Collection<int, ProductPrice>
     */
    public function findByProductId(int $productId): Collection;

    /**
     * Create a new price for a product.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): ProductPrice;
}

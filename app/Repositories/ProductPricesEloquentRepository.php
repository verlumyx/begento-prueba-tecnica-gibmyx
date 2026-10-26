<?php

namespace App\Repositories;

use App\Models\ProductPrice;
use App\Repositories\Contracts\ProductPricesRepository;
use Illuminate\Database\Eloquent\Collection;

class ProductPricesEloquentRepository implements ProductPricesRepository
{
    /**
     * Get all prices for a given product ID.
     *
     * @return Collection<int, ProductPrice>
     */
    public function findByProductId(int $productId): Collection
    {
        return ProductPrice::where('product_id', $productId)->get();
    }

    /**
     * Create a new price for a product.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): ProductPrice
    {
        return ProductPrice::create($data);
    }
}

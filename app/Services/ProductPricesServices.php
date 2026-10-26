<?php

namespace App\Services;

use App\Exceptions\ProductNotFoundException;
use App\Models\ProductPrice;
use App\Repositories\Contracts\ProductPricesRepository;
use App\Repositories\Contracts\ProductsRepository;
use Illuminate\Database\Eloquent\Collection;

class ProductPricesServices
{
    public function __construct(
        private ProductsRepository $productsRepository,
        private ProductPricesRepository $productPricesRepository
    ) {}

    /**
     * Get all prices of a product by product ID or throw a ProductNotFoundException.
     *
     * @return Collection<int, ProductPrice>
     *
     * @throws ProductNotFoundException
     */
    public function findByProductId(int $productId): Collection
    {
        $product = $this->productsRepository->findById($productId);

        if (! $product) {
            throw new ProductNotFoundException;
        }

        return $this->productPricesRepository->findByProductId($productId);
    }

    /**
     * Create a new price for a product or throw a ProductNotFoundException.
     *
     * @param  array<string, mixed>  $data
     *
     * @throws ProductNotFoundException
     */
    public function create(int $productId, array $data): ProductPrice
    {
        $product = $this->productsRepository->findById($productId);

        if (! $product) {
            throw new ProductNotFoundException;
        }

        $data['product_id'] = $productId;

        return $this->productPricesRepository->create($data);
    }
}

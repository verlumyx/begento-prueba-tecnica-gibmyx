<?php

namespace Tests\Unit;

use App\Exceptions\ProductNotFoundException;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Repositories\Contracts\ProductPricesRepository;
use App\Repositories\Contracts\ProductsRepository;
use App\Services\ProductPricesServices;
use Illuminate\Database\Eloquent\Collection;
use Mockery;
use Tests\TestCase;

class ProductPricesServicesTest extends TestCase
{
    public function test_find_by_product_id_returns_collection_when_product_exists(): void
    {
        $productId = 1;
        $product = new Product(['id' => $productId, 'name' => 'Laptop Gamer']);
        $product->id = $productId;

        $price1 = new ProductPrice(['id' => 1, 'product_id' => $productId, 'currency_id' => 1, 'price' => 1200.00]);
        $price2 = new ProductPrice(['id' => 2, 'product_id' => $productId, 'currency_id' => 2, 'price' => 1104.00]);
        $expectedCollection = new Collection([$price1, $price2]);

        $mockProductsRepo = Mockery::mock(ProductsRepository::class);
        $mockProductsRepo->shouldReceive('findById')
            ->once()
            ->with($productId)
            ->andReturn($product);

        $mockPricesRepo = Mockery::mock(ProductPricesRepository::class);
        $mockPricesRepo->shouldReceive('findByProductId')
            ->once()
            ->with($productId)
            ->andReturn($expectedCollection);

        $service = new ProductPricesServices($mockProductsRepo, $mockPricesRepo);
        $result = $service->findByProductId($productId);

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(2, $result);
        $this->assertEquals(1200.00, $result[0]->price);
    }

    public function test_find_by_product_id_throws_product_not_found_exception_when_product_does_not_exist(): void
    {
        $this->expectException(ProductNotFoundException::class);
        $this->expectExceptionMessage('El producto solicitado no fue encontrado.');

        $productId = 999;

        $mockProductsRepo = Mockery::mock(ProductsRepository::class);
        $mockProductsRepo->shouldReceive('findById')
            ->once()
            ->with($productId)
            ->andReturn(null);

        $mockPricesRepo = Mockery::mock(ProductPricesRepository::class);
        $mockPricesRepo->shouldNotReceive('findByProductId');

        $service = new ProductPricesServices($mockProductsRepo, $mockPricesRepo);
        $service->findByProductId($productId);
    }

    public function test_create_calls_repository_and_returns_created_price_when_product_exists(): void
    {
        $productId = 1;
        $product = new Product(['id' => $productId, 'name' => 'Laptop Gamer']);
        $product->id = $productId;

        $inputData = [
            'currency_id' => 2,
            'price' => 1104.46,
        ];

        $expectedPrice = new ProductPrice(array_merge($inputData, [
            'id' => 10,
            'product_id' => $productId,
        ]));

        $mockProductsRepo = Mockery::mock(ProductsRepository::class);
        $mockProductsRepo->shouldReceive('findById')
            ->once()
            ->with($productId)
            ->andReturn($product);

        $mockPricesRepo = Mockery::mock(ProductPricesRepository::class);
        $mockPricesRepo->shouldReceive('create')
            ->once()
            ->with(array_merge($inputData, ['product_id' => $productId]))
            ->andReturn($expectedPrice);

        $service = new ProductPricesServices($mockProductsRepo, $mockPricesRepo);
        $result = $service->create($productId, $inputData);

        $this->assertInstanceOf(ProductPrice::class, $result);
        $this->assertEquals($productId, $result->product_id);
        $this->assertEquals(2, $result->currency_id);
        $this->assertEquals(1104.46, $result->price);
    }

    public function test_create_throws_product_not_found_exception_when_product_does_not_exist(): void
    {
        $this->expectException(ProductNotFoundException::class);
        $this->expectExceptionMessage('El producto solicitado no fue encontrado.');

        $productId = 999;
        $inputData = [
            'currency_id' => 2,
            'price' => 1104.46,
        ];

        $mockProductsRepo = Mockery::mock(ProductsRepository::class);
        $mockProductsRepo->shouldReceive('findById')
            ->once()
            ->with($productId)
            ->andReturn(null);

        $mockPricesRepo = Mockery::mock(ProductPricesRepository::class);
        $mockPricesRepo->shouldNotReceive('create');

        $service = new ProductPricesServices($mockProductsRepo, $mockPricesRepo);
        $service->create($productId, $inputData);
    }
}

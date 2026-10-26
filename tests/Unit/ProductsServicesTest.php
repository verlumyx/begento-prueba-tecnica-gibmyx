<?php

namespace Tests\Unit;

use App\Exceptions\ProductNotFoundException;
use App\Models\Product;
use App\Repositories\Contracts\ProductsRepository;
use App\Services\ProductsServices;
use Illuminate\Pagination\CursorPaginator;
use Mockery;
use Tests\TestCase;

class ProductsServicesTest extends TestCase
{
    public function test_create_calls_repository_and_returns_created_product(): void
    {
        $productData = [
            'name' => 'Laptop Gamer Pro',
            'description' => 'Laptop de alto rendimiento',
            'price' => 1200.50,
            'currency_id' => 1,
            'tax_cost' => 192.08,
            'manufacturing_cost' => 750.00,
        ];

        $expectedProduct = new Product(array_merge($productData, ['id' => 1]));

        $mockRepository = Mockery::mock(ProductsRepository::class);
        $mockRepository->shouldReceive('create')
            ->once()
            ->with($productData)
            ->andReturn($expectedProduct);

        $service = new ProductsServices($mockRepository);
        $result = $service->create($productData);

        $this->assertInstanceOf(Product::class, $result);
        $this->assertEquals($expectedProduct->id, $result->id);
        $this->assertEquals('Laptop Gamer Pro', $result->name);
        $this->assertEquals(1200.50, $result->price);
    }

    public function test_cursor_paginate_calls_repository_with_given_per_page(): void
    {
        $paginator = new CursorPaginator(collect([]), 10);

        $mockRepository = Mockery::mock(ProductsRepository::class);
        $mockRepository->shouldReceive('cursorPaginate')
            ->once()
            ->with(10)
            ->andReturn($paginator);

        $service = new ProductsServices($mockRepository);
        $result = $service->cursorPaginate(10);

        $this->assertInstanceOf(CursorPaginator::class, $result);
        $this->assertEquals(10, $result->perPage());
    }

    public function test_find_by_id_returns_product_when_found(): void
    {
        $product = new Product(['name' => 'Teclado Mecanico']);
        $product->id = 5;

        $mockRepository = Mockery::mock(ProductsRepository::class);
        $mockRepository->shouldReceive('findById')
            ->once()
            ->with(5)
            ->andReturn($product);

        $service = new ProductsServices($mockRepository);
        $result = $service->findById(5);

        $this->assertInstanceOf(Product::class, $result);
        $this->assertEquals(5, $result->id);
        $this->assertEquals('Teclado Mecanico', $result->name);
    }

    public function test_find_by_id_throws_product_not_found_exception_when_not_found(): void
    {
        $this->expectException(ProductNotFoundException::class);
        $this->expectExceptionMessage('El producto solicitado no fue encontrado.');

        $mockRepository = Mockery::mock(ProductsRepository::class);
        $mockRepository->shouldReceive('findById')
            ->once()
            ->with(999)
            ->andReturn(null);

        $service = new ProductsServices($mockRepository);
        $service->findById(999);
    }

    public function test_delete_calls_repository_and_returns_true_when_successful(): void
    {
        $mockRepository = Mockery::mock(ProductsRepository::class);
        $mockRepository->shouldReceive('delete')
            ->once()
            ->with(1)
            ->andReturn(true);

        $service = new ProductsServices($mockRepository);
        $result = $service->delete(1);

        $this->assertTrue($result);
    }

    public function test_delete_throws_product_not_found_exception_when_not_found(): void
    {
        $this->expectException(ProductNotFoundException::class);
        $this->expectExceptionMessage('El producto solicitado no fue encontrado.');

        $mockRepository = Mockery::mock(ProductsRepository::class);
        $mockRepository->shouldReceive('delete')
            ->once()
            ->with(999)
            ->andReturn(false);

        $service = new ProductsServices($mockRepository);
        $service->delete(999);
    }
}

<?php

namespace Tests\Unit;

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
}

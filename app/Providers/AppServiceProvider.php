<?php

namespace App\Providers;

use App\Repositories\Contracts\CurrenciesRepository;
use App\Repositories\Contracts\ProductsRepository;
use App\Repositories\CurrenciesEloquentRepository;
use App\Repositories\ProductsEloquentRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            CurrenciesRepository::class,
            CurrenciesEloquentRepository::class
        );

        $this->app->bind(
            ProductsRepository::class,
            ProductsEloquentRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

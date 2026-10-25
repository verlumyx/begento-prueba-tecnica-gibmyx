<?php

namespace App\Providers;

use App\Repositories\Contracts\CurrenciesRepository;
use App\Repositories\CurrenciesEloquentRepository;
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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

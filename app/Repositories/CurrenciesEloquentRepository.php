<?php

namespace App\Repositories;

use App\Models\Currency;
use App\Repositories\Contracts\CurrenciesRepository;
use Illuminate\Support\Collection;

class CurrenciesEloquentRepository implements CurrenciesRepository
{

    public function all(): Collection
    {
        return Currency::get();
    }

}

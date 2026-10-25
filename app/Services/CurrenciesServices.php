<?php

namespace App\Services;

use App\Repositories\Contracts\CurrenciesRepository;

use Illuminate\Support\Collection;

class CurrenciesServices
{
    public function __construct(private CurrenciesRepository $currenciesRepository)
    {
    }

    public function all(): Collection
    {
        return $this->currenciesRepository->all();
    }
}

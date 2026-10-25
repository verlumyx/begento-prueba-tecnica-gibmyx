<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface CurrenciesRepository
{
    public function all(): Collection;

}

<?php

namespace App\Connector\CurrencyRates;

use App\Utility\Collection;

interface RatesResolver
{
    /**
     * @return Collection
     */
    public function getRatesCollection() : Collection;
}

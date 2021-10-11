<?php

namespace App\Connector\CurrencyRates;

use App\Utility\Collection;

interface RatesRetrieval
{
    /**
     * @return Collection
     */
    public function getRatesCollection() : Collection;
}

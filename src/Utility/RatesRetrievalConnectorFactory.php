<?php

namespace App\Utility;

use App\Connector\CurrencyRates\RatesRetrieval;
use App\Connector\CurrencyRates\Ecb;
use App\Connector\CurrencyRates\Cbr;

class RatesRetrievalConnectorFactory
{
    /**
     * @param string $source
     * @return RatesRetrieval
     */
    public static function createRatesRetrievalConnector(string $source): RatesRetrieval
    {
        $connectors = [
            Ecb::class => function () {
                return new Ecb();
            },
            Cbr::class => function () {
                return new Cbr();
            }
        ];

        return call_user_func($connectors["App\Connector\CurrencyRates\\$source"]);
    }

}
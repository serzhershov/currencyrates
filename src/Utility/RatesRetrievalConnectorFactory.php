<?php

namespace App\Utility;

use App\Connector\CurrencyRates\RatesResolver;
use App\Connector\CurrencyRates\Ecb;
use App\Connector\CurrencyRates\Cbr;

/**
 * @todo: remove in favor of services.yaml configured dependency injection
 */
class RatesRetrievalConnectorFactory
{
    /**
     * @param string $source
     * @return RatesResolver
     */
    public static function createRatesRetrievalConnector(string $source): RatesResolver
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
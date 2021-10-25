<?php

namespace App\Utility;

use App\Connector\CurrencyRates\RatesResolver;

class RatesResolverFactory
{

    /**
     * @param string $source
     * @param iterable $resolvers
     * @return RatesResolver
     */
    public static function provideResolver(string $source, iterable $resolvers): RatesResolver
    {
        /** @var RatesResolver $resolver */
        foreach ($resolvers as $resolver) {
            if ($resolver->getSource() === $source) {
                return $resolver;
            }
        }
        throw new \RuntimeException("Resolver not set for [$source] source");
    }

}
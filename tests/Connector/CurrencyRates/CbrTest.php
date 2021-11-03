<?php

namespace App\Tests\Connector\CurrencyRates;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Connector\CurrencyRates\Cbr;

/**
 * @covers \App\Connector\CurrencyRates\Cbr
 * @covers \App\Connector\HttpConnector
 * @uses   \App\Connector\CurrencyRates\CurrencyRates
 */
final class CbrTest extends KernelTestCase
{
    public function testCbrConnectorPerformsAsExpected(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $connector = $container->get(Cbr::class);
        $ratesCollection = $connector->getRatesCollection();
        $this->assertIsObject($ratesCollection, 'Collection was not parsed from connector response');
        $this->assertNotEmpty($ratesCollection, 'Collection is empty');
    }
}
<?php

namespace App\Tests\Connector\CurrencyRates;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Connector\CurrencyRates\Ecb;

/**
 * @covers \App\Connector\CurrencyRates\Ecb
 * @covers \App\Connector\HttpConnector
 * @uses   \App\Connector\CurrencyRates\CurrencyRates
 */
final class EcbTest extends KernelTestCase
{
    public function testEcbConnectorPerformsAsExpected(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $connector = $container->get(Ecb::class);
        $ratesCollection = $connector->getRatesCollection();
        $this->assertIsObject($ratesCollection, 'Collection was not parsed from connector response');
        $this->assertNotEmpty($ratesCollection, 'Collection is empty');
    }
}
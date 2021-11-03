<?php

namespace App\Tests\Connector\CurrencyRates;

use App\Connector\CurrencyRates\CurrencyRates;
use PHPUnit\Framework\TestCase;

/**
 * @covers CurrencyRates
 */
final class CurrencyRatesTest extends TestCase
{
    /**
     * @dataProvider validCurrencyParameters
     */
    public function testValidCurrencyRatesDTOCanBeCreated(
        $source,
        $currencyIsoCode,
        $baseCurrency,
        $rate,
        $nominal,
        $rateDate
    ): void
    {
        $rateObejct = new CurrencyRates(
            $source,
            $currencyIsoCode,
            $baseCurrency,
            $rate,
            $nominal,
            $rateDate
        );
        $this->assertTrue($rateObejct instanceof CurrencyRates);
        $this->assertEquals($source, $rateObejct->getSource());
        $this->assertEquals($currencyIsoCode, $rateObejct->getCurrencyIsoCode());
        $this->assertEquals($baseCurrency, $rateObejct->getBaseCurrency());
        $this->assertEquals($rate, $rateObejct->getRate());
        $this->assertEquals($nominal, $rateObejct->getNominal());
        $this->assertEquals($rateDate, $rateObejct->getRateDate());
    }

    /**
     * @dataProvider invalidCurrencyParameters
     */
    public function testInvalidCurrencyRatesDTOCanNotBeCreated(
        $source,
        $currencyIsoCode,
        $baseCurrency,
        $rate,
        $nominal,
        $rateDate
    ): void
    {
        $this->expectException('InvalidArgumentException');
        $rateObject = new CurrencyRates(
            $source,
            $currencyIsoCode,
            $baseCurrency,
            $rate,
            $nominal,
            $rateDate
        );
    }

    public function validCurrencyParameters(): array
    {
        return ['expected data' => ['Ecb', 'USD', 'EUR', '1.20', 1, new \DateTime()]];
    }

    public function invalidCurrencyParameters(): array
    {
        return ['error data' => ['Ecb', 'NIKKEI', 'CEBU', '0.90', 1, new \DateTime()]];
    }
}
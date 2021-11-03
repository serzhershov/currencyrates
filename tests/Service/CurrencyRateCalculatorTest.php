<?php

namespace App\Tests\Service;

use App\Connector\CurrencyRates\CurrencyRates;
use App\Service\CurrencyRateCalculator;
use App\Repository\ExchangeRateRepository;
use App\ValueObject\CurrencyConversionRequest;
use App\ValueObject\CurrencyConversionResult;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @covers \App\Service\CurrencyRateCalculator
 * @uses \App\ValueObject\CurrencyConversionRequest;
 * @uses \App\ValueObject\CurrencyConversionResult;
 */
class CurrencyRateCalculatorTest extends KernelTestCase
{
    /**
     * @var CurrencyRateCalculator|object|null
     */
    private $calculatorService;
    /**
     * @var ExchangeRateRepository|object|null
     */
    private $exchangeRateRepo;
    /**
     * @var string
     */
    private $appSource;

    public function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $this->appSource = $container->getParameter('app.source');
        $this->exchangeRateRepo = $container->get(ExchangeRateRepository::class);
        $this->calculatorService = $container->get(CurrencyRateCalculator::class);

        parent::setUp();
    }

    public function testEmptyDatabaseFailsCalculationAttempt(): void
    {
        $exchangeRateRepoMock = $this->createMock(ExchangeRateRepository::class);
        $calculatorService = new CurrencyRateCalculator($exchangeRateRepoMock);
        $this->expectException('RuntimeException');
        $calculatorService->convert(new CurrencyConversionRequest('EUR', 'RUB', 100));
    }

    /**
     * @dataProvider validCurrencyRates
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testPreFilledDatabaseAllowsCalculationAttempt(
        $currencyIsoCode,
        $baseCurrency,
        $rate,
        $nominal,
        $rateDate
    ): void
    {
        $this->exchangeRateRepo->persistRatesCollection(new \App\Utility\Collection([
            new CurrencyRates(
                $this->appSource,
                $currencyIsoCode,
                $baseCurrency,
                $rate,
                $nominal,
                $rateDate
            )
        ]));
        $this->assertCount(1, $this->exchangeRateRepo->findAll());
        $conversionResult = $this->calculatorService->convert(new CurrencyConversionRequest('EUR', 'USD', 100));
        $this->assertInstanceOf('App\ValueObject\CurrencyConversionResult', $conversionResult);
    }

    public function validCurrencyRates(): array
    {
        return ['expected data' => ['USD', 'EUR', '1.20', 1, new \DateTime()]];
    }
}
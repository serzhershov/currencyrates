<?php

namespace App\Service;

use App\Entity\ExchangeRate;
use App\Repository\ExchangeRateRepository;
use App\ValueObject\CurrencyConversionRequest;
use App\ValueObject\CurrencyConversionResult;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Intl\Currencies;

final class CurrencyRateCalculator
{
    /**
     * @var ExchangeRateRepository
     */
    private ExchangeRateRepository $rateRepository;
    /**
     * @var string
     */
    private string $appSource;

    /**
     * @param ExchangeRateRepository $exchangeRateRepository
     */
    public function __construct(
        ExchangeRateRepository $exchangeRateRepository
    )
    {
        $this->rateRepository = $exchangeRateRepository;
    }

    /**
     * @param string $appSource
     */
    public function setAppSource(string $appSource)
    {
        $this->appSource = $appSource;
    }

    /**
     * @param CurrencyConversionRequest $conversionRequest
     * @return CurrencyConversionResult
     * @throws \RuntimeException
     */
    public function convert(CurrencyConversionRequest $conversionRequest): CurrencyConversionResult
    {
        $baseCurrency = $this->rateRepository->getBaseCurrencyIsoCode();
        if (!$baseCurrency) {
            throw new \RuntimeException(
                'No data was imported from the selected source, no calculations can be done. Please run at least one currency import'
            );
        }

        $sourceRate = '1';
        \bcscale(10);
        if ($conversionRequest->getSourceCurrency() !== $baseCurrency) {
            $latestSourceData = $this->rateRepository->getLatestCurrencyRates($conversionRequest->getSourceCurrency());
            if (!$latestSourceData) {
                throw new \RuntimeException('Source currency data not found');
            }
            $sourceRate = \bcdiv($latestSourceData->getRate(), $latestSourceData->getNominal());
        }

        $destinationRate = '1';
        if ($conversionRequest->getTargetCurrency() !== $baseCurrency) {
            $latestDestinationData = $this->rateRepository->getLatestCurrencyRates($conversionRequest->getTargetCurrency());
            if (!$latestDestinationData) {
                throw new \RuntimeException('Destination currency data not found');
            }
            $destinationRate = \bcdiv($latestDestinationData->getRate(), $latestDestinationData->getNominal());
        }

        $dividedRates = \bcdiv($sourceRate, $destinationRate);
        if ($this->appSource === 'ecb') {
            $conversionResult = \bcdiv($conversionRequest->getAmount(), $dividedRates);
        } else {
            $conversionResult = \bcmul($conversionRequest->getAmount(), $dividedRates);
        }

        return new CurrencyConversionResult(
            $conversionRequest->getSourceCurrency(),
            $conversionRequest->getTargetCurrency(),
            number_format($conversionResult, Currencies::getFractionDigits($conversionRequest->getTargetCurrency()), '.', ''),
            $conversionRequest
        );
    }
}

<?php

namespace App\Controller\Service;

use App\Connector\CurrencyRates\CurrencyRates;
use App\ValueObject\CurrencyConversionRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CurrencyRatesController extends AbstractController
{
    /**
     * @todo: move to \App\Repository\ExchangeRateRepository
     * @param CurrencyRates $currencyRatesDTO
     * @return void
     */
    public function saveCurrencyRatesByDto(CurrencyRates $currencyRatesDTO): void
    {
        $em = $this->getDoctrine()->getManager();
        $ratesRecord = \App\Entity\ExchangeRate::makeFromCurrencyRatesDTO($currencyRatesDTO);
        $em->persist($ratesRecord);
        $em->flush();
    }

    /**
     * @param CurrencyConversionRequest $conversionRequest
     */
    public function convert(CurrencyConversionRequest $conversionRequest)
    {
        //@todo: implement validation of the incoming request,
        // provide the conversion via bcmath and apply correct formula depending on source/parameter
        // return conversionResult vo
    }
}

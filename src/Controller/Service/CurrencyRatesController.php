<?php

namespace App\Controller\Service;

use App\Connector\CurrencyRates\CurrencyRates;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CurrencyRatesController extends AbstractController
{
    /**
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
}

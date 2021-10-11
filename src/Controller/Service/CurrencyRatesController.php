<?php

namespace App\Controller\Service;

use App\Connector\CurrencyRates\CurrencyRatesDTO;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CurrencyRatesController extends AbstractController
{
    /**
     * @param CurrencyRatesDTO $currencyRatesDTO
     * @return void
     */
    public function saveCurrencyRatesByDto(CurrencyRatesDTO $currencyRatesDTO): void
    {
        $em = $this->getDoctrine()->getManager();
        $ratesRecord = \App\Entity\ExchangeRate::makeFromCurrencyRatesDTO($currencyRatesDTO);
        $em->persist($ratesRecord);
        $em->flush();
    }
}

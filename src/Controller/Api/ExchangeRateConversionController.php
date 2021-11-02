<?php

namespace App\Controller\Api;

use App\Service\CurrencyRateCalculator;
use App\ValueObject\CurrencyConversionRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


class ExchangeRateConversionController extends AbstractController
{
    /**
     * @Route("/api", name="index", methods={"GET"})
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return new JsonResponse("Pong");
    }

    /**
     * @Route("/api/exchange/{currencyFrom}/{currencyTo}/{amount}", name="get_exchange_quote", methods={"GET"})
     * @param $currencyFrom string
     * @param $currencyTo string
     * @param $request Request
     * @return JsonResponse
     */
    public function getExchangeQuote(
        string $currencyFrom,
        string $currencyTo,
        int $amount,
        CurrencyRateCalculator $currencyRateCalculator
    ) : JsonResponse
    {
        try {
            $conversionResult = $currencyRateCalculator->convert(
                new CurrencyConversionRequest($currencyFrom, $currencyTo, $amount)
            );
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(
                ['constraint_validation_errors' => $e->getMessage()],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        } catch (\RuntimeException $e) {
            return new JsonResponse(
                ['service_error' => $e->getMessage()],
                Response::HTTP_FAILED_DEPENDENCY
            );
        }

        return new JsonResponse([
            'converted_amount' => $conversionResult->getAmount(),
            'request' => $conversionResult->getOriginalRequest()->toArray()
        ]);
    }

}

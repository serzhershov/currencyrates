<?php

namespace App\Controller\Api;

use App\Entity\ExchangeRate;
use http\Exception\RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Intl\Currencies;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Validator\Constraints\Currency;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validation;


class Convert extends AbstractController
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
    public function getExchangeQuote(string $currencyFrom, string $currencyTo, int $amount, Request $request): JsonResponse
    {
        $validator = Validation::createValidator();
        $violations = [
            'from' => function () use ($validator, $currencyFrom) {
                return $validator->validate($currencyFrom, [new NotBlank(), new Currency()]);
            },
            'to' => function () use ($validator, $currencyTo) {
                return $validator->validate($currencyTo, [new NotBlank(), new Currency()]);
            },
            'amount' => function () use ($validator, $amount) {
                return $validator->validate($amount, [new NotNull(), new Range(['min' => 1, 'max' => PHP_INT_MAX])]);
            },
        ];
        $errorData = [];
        foreach ($violations as $type => $closure) {
            /** @var ConstraintViolationListInterface $violation */
            $violation = $closure();
            if (0 !== count($violation)) {
                /** @var ConstraintViolation $violationData */
                foreach ($violation as $violationData) {
                    $errorData[$violationData->getInvalidValue()] = $violationData->getMessage();
                }
            }
        }
        if (count($errorData)) {
            return new JsonResponse(['Constraint_validation_errors' => $errorData]);
        }

        if ($currencyFrom === $currencyTo) {
            return new JsonResponse(['requested_amount' => $amount ,'converted_amount' => $amount, 'direction' => "$currencyFrom -> $currencyTo"]);
        }

        $ratesRepository = $this->getDoctrine()->getRepository(ExchangeRate::class);
        /** @var ExchangeRate $baseCurrency */
        $baseCurrencyData = $ratesRepository->findOneBy(
            ['source' => $this->getParameter('rates_connector_source')],
            ['rate_date' => 'ASC', 'created' => 'ASC']
        );
        if (!$baseCurrencyData) {
            throw new RuntimeException(
                'No data was imported from the selected source, no calculations can be done. Please run at least one currency import'
            );
        }

        $baseCurrency = $baseCurrencyData->getBaseCurrency();

        if ($currencyFrom === $baseCurrency) {
            $sourceRate = 1;
        } else {
            /** @var ExchangeRate $latestSourceData */
            $latestSourceData = $ratesRepository->findOneBy(
                ['source' => $this->getParameter('rates_connector_source'), 'currency_iso_code' => $currencyFrom],
                ['rate_date' => 'ASC', 'created' => 'ASC']
            );
            if (!$latestSourceData) {
                throw new RuntimeException('Source currency data not found');
            }
            $sourceRate = $latestSourceData->getRate() / $latestSourceData->getNominal();
        }

        if ($currencyTo === $baseCurrency) {
            $destinationRate = 1;
        } else {
            /** @var ExchangeRate $latestDestinationData */
            $latestDestinationData = $ratesRepository->findOneBy(
                ['source' => $this->getParameter('rates_connector_source'), 'currency_iso_code' => $currencyTo],
                ['rate_date' => 'ASC', 'created' => 'ASC']
            );
            if (!$latestDestinationData) {
                throw new RuntimeException('Destination currency data not found');
            }
            $destinationRate = $latestDestinationData->getRate() / $latestDestinationData->getNominal();
        }

        // i am ashamed =_= but let me explain, how a person with MBA in business administration still can't convert currencies
        if ($this->getParameter('rates_connector_source') === 'Ecb') {
            $conversionResult = number_format($amount / ($sourceRate / $destinationRate), Currencies::getFractionDigits($currencyTo), '.', '');
        } else {
            $conversionResult = number_format($amount * ($sourceRate / $destinationRate), Currencies::getFractionDigits($currencyTo), '.', '');
        }

        return new JsonResponse(['requested_amount' => $amount ,'converted_amount' => $conversionResult, 'direction' => "$currencyFrom -> $currencyTo"]);
    }

}

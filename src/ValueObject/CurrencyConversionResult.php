<?php

namespace App\ValueObject;

use App\Utility\ConstraintViolationListParser;
use App\ValueObject\Traits\ToArray;
use \Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;

/**
 * Result of processing the currency conversion operation
 * string $sourceCurrency, string $targetCurrency, string $amount
 */
final class CurrencyConversionResult
{
    use ToArray;

    /**
     * iso currency code of the source currency
     * @var string
     * @Assert\Currency
     * @Assert\NotBlank
     */
    private string $sourceCurrency;

    /**
     * iso currency code of the target currency
     * @var string
     * @Assert\Currency
     * @Assert\NotBlank
     */
    private string $targetCurrency;

    /**
     * @var string
     * @Assert\Positive()
     */
    private string $amount;

    /**
     * @var CurrencyConversionRequest
     * @Assert\Type("\App\ValueObject\CurrencyConversionRequest")
     */
    private CurrencyConversionRequest $originalRequest;

    /**
     * @param string $sourceCurrency
     * @param string $targetCurrency
     * @param string $amount
     * @param CurrencyConversionRequest $request
     */
    public function __construct(string $sourceCurrency, string $targetCurrency, string $amount, CurrencyConversionRequest $request)
    {
        $this->sourceCurrency = mb_strtoupper($sourceCurrency);
        $this->targetCurrency = mb_strtoupper($targetCurrency);
        $this->amount = $amount;
        $this->originalRequest = $request;

        $validator = Validation::createValidatorBuilder()
            ->enableAnnotationMapping(true)
            ->addDefaultDoctrineAnnotationReader()
            ->getValidator();

        $violation = $validator->validate($this);
        if (count($violation)) {
            throw new \InvalidArgumentException(ConstraintViolationListParser::getString($violation));
        }
    }

    /**
     * @return string
     */
    public function getSourceCurrency(): string
    {
        return $this->sourceCurrency;
    }

    /**
     * @return string
     */
    public function getTargetCurrency(): string
    {
        return $this->targetCurrency;
    }

    /**
     * @return string
     */
    public function getAmount(): string
    {
        return $this->amount;
    }

    /**
     * @return CurrencyConversionRequest
     */
    public function getOriginalRequest(): CurrencyConversionRequest
    {
        return $this->originalRequest;
    }

    /**
     * @param CurrencyConversionRequest $originalRequest
     */
    public function setOriginalRequest(CurrencyConversionRequest $originalRequest): void
    {
        $this->originalRequest = $originalRequest;
    }

}
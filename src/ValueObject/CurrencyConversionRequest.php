<?php

namespace App\ValueObject;

use App\Utility\ConstraintViolationListParser;
use App\ValueObject\Traits\ToArray;
use \Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;

/**
 * Request to process currency conversion
 * string $sourceCurrency, string $targetCurrency, string $amount
 */
final class CurrencyConversionRequest
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
     * @param string $sourceCurrency
     * @param string $targetCurrency
     * @param string $amount
     */
    public function __construct(string $sourceCurrency, string $targetCurrency, string $amount)
    {
        $this->sourceCurrency = mb_strtoupper($sourceCurrency);
        $this->targetCurrency = mb_strtoupper($targetCurrency);
        $this->amount = $amount;

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
}
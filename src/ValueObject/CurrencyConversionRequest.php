<?php

namespace App\ValueObject;

use \Symfony\Component\Validator\Constraints as Assert;

class CurrencyConversionRequest
{
    /**
     * iso currency code of the source currency
     * @var string
     * @Assert\Currency
     * @Assert\NotBlank
     */
    private $sourceCurrency;

    /**
     * iso currency code of the target currency
     * @var string
     * @Assert\Currency
     * @Assert\NotBlank
     */
    private $targetCurrency;

    /**
     * @var string
     * @Assert\Positive()
     */
    private $amount;

    public function __construct(string $sourceCurrency, string $targetCurrency, string $amount)
    {
        $this->sourceCurrency = $sourceCurrency;
        $this->targetCurrency = $targetCurrency;
        $this->amount = $amount;
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
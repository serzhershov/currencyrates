<?php

namespace App\Connector\CurrencyRates;

use App\Utility\ConstraintViolationListParser;
use \Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;

final class CurrencyRates
{
    /**
     * iso currency code of the target currency
     * @var string
     * @Assert\Currency
     * @Assert\NotBlank
     */
    private string $currencyIsoCode;

    /**
     * iso currency code of the base currency
     * @var string
     * @Assert\Currency
     * @Assert\NotBlank
     */
    private string $baseCurrency;

    /**
     * rate
     * @var string
     * @Assert\NotBlank
     */
    private string $rate;

    /**
     * @var int
     * @Assert\NotNull
     */
    private int $nominal;

    /**
     * date when the given rate was actual fot
     * @var \DateTime
     * @Assert\Type("\DateTime")
     * @Assert\NotBlank
     */
    private \DateTime $rateDate;

    /**
     * rate
     * @var string
     * @Assert\NotBlank
     */
    private string $source;

    /**
     * @param string $source
     * @param string $currencyIsoCode
     * @param string $baseCurrency
     * @param string $rate
     * @param int $nominal
     * @param \DateTime $rateDate
     */
    public function __construct(
        string    $source,
        string    $currencyIsoCode,
        string    $baseCurrency,
        string    $rate,
        int       $nominal,
        \DateTime $rateDate
    )
    {
        $this->source = $source;
        $this->currencyIsoCode = $currencyIsoCode;
        $this->baseCurrency = $baseCurrency;
        $this->rate = $rate;
        $this->nominal = $nominal;
        $this->rateDate = $rateDate;

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
    public function getCurrencyIsoCode(): string
    {
        return $this->currencyIsoCode;
    }

    /**
     * @return string
     */
    public function getBaseCurrency(): string
    {
        return $this->baseCurrency;
    }

    /**
     * @return string
     */
    public function getRate(): string
    {
        return $this->rate;
    }

    /**
     * @return int
     */
    public function getNominal(): int
    {
        return $this->nominal;
    }

    /**
     * @return \DateTime
     */
    public function getRateDate(): \DateTime
    {
        return $this->rateDate;
    }

    /**
     * @return string
     */
    public function getSource(): string
    {
        return $this->source;
    }
}
<?php

namespace App\Connector\CurrencyRates;

use App\Utility\ConstraintViolationListParser;
use \Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;

class CurrencyRates
{
    /**
     * iso currency code of the target currency
     * @var string
     * @Assert\Currency
     * @Assert\NotBlank
     */
    private $currency_iso_code;

    /**
     * iso currency code of the base currency
     * @var string
     * @Assert\Currency
     * @Assert\NotBlank
     */
    private $base_currency;

    /**
     * rate
     * @var string
     * @Assert\NotBlank
     */
    private $rate;

    /**
     * @var int
     * @Assert\NotNull
     */
    private $nominal;

    /**
     * date when the given rate was actual fot
     * @var \DateTime
     * @Assert\Type("\DateTime")
     * @Assert\NotBlank
     */
    private $rate_date;

    /**
     * rate
     * @var string
     * @Assert\NotBlank
     */
    private $source;

    /**
     * @param string $source
     * @param string $currency_iso_code
     * @param string $base_currency
     * @param string $rate
     * @param int $nominal
     * @param \DateTime $rate_date
     */
    public function __construct(
        string $source,
        string $currency_iso_code,
        string $base_currency,
        string $rate,
        int $nominal,
        \DateTime $rate_date
    )
    {
        $this->source = $source;
        $this->currency_iso_code = $currency_iso_code;
        $this->base_currency = $base_currency;
        $this->rate = $rate;
        $this->nominal = $nominal;
        $this->rate_date = $rate_date;

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
        return $this->currency_iso_code;
    }

    /**
     * @return string
     */
    public function getBaseCurrency(): string
    {
        return $this->base_currency;
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
        return $this->rate_date;
    }

    /**
     * @return string
     */
    public function getSource(): string
    {
        return $this->source;
    }
}
<?php

namespace App\Entity;

use App\Connector\CurrencyRates\CurrencyRates;
use App\Repository\ExchangeRateRepository;
use App\Utility\ConstraintViolationListParser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Validation;

/**
 * @ORM\Entity(repositoryClass=ExchangeRateRepository::class)
 */
class ExchangeRate
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @ORM\Column(type="string", length=3, name="currency_iso_code")
     */
    private $currencyIsoCode;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $rate;

    /**
     * @ORM\Column(type="string", length=3, name="base_currency")
     */
    private $baseCurrency;

    /**
     * @ORM\Column(type="integer")
     */
    private $nominal;

    /**
     * @ORM\Column(type="datetime", name="rate_date")
     */
    private $rateDate;

    /**
     * @ORM\Column(type="string", length=3)
     */
    private $source;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return ExchangeRate
     */
    public function setId($id): ExchangeRate
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param mixed $created
     * @return ExchangeRate
     */
    public function setCreated($created): ExchangeRate
    {
        $this->created = $created;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCurrencyIsoCode()
    {
        return $this->currencyIsoCode;
    }

    /**
     * @param mixed $currencyIsoCode
     * @return ExchangeRate
     */
    public function setCurrencyIsoCode($currencyIsoCode): ExchangeRate
    {
        $this->currencyIsoCode = $currencyIsoCode;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * @param mixed $rate
     * @return ExchangeRate
     */
    public function setRate($rate): ExchangeRate
    {
        $this->rate = $rate;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBaseCurrency()
    {
        return $this->baseCurrency;
    }

    /**
     * @param mixed $baseCurrency
     * @return ExchangeRate
     */
    public function setBaseCurrency($baseCurrency): ExchangeRate
    {
        $this->baseCurrency = $baseCurrency;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNominal()
    {
        return $this->nominal;
    }

    /**
     * @param mixed $nominal
     * @return ExchangeRate
     */
    public function setNominal($nominal): ExchangeRate
    {
        $this->nominal = $nominal;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRateDate()
    {
        return $this->rateDate;
    }

    /**
     * @param mixed $rateDate
     * @return ExchangeRate
     */
    public function setRateDate($rateDate): ExchangeRate
    {
        $this->rateDate = $rateDate;
        return $this;
    }


    /**
     * @return mixed
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param string $source
     * @return ExchangeRate
     */
    public function setSource(string $source): ExchangeRate
    {
        $this->source = $source;
        return $this;
    }

    /**
     * @param string $baseCurrency
     * @param string $currencyIsoCode
     * @param int $nominal
     * @param string $rate
     * @param \DateTime $rateDate
     * @param string $source
     */
    public function __construct(
        string    $baseCurrency,
        string    $currencyIsoCode,
        int       $nominal,
        string    $rate,
        \DateTime $rateDate,
        string    $source
    )
    {
            $this->setCreated(new \DateTime());
            $this->setBaseCurrency($baseCurrency);
            $this->setCurrencyIsoCode($currencyIsoCode);
            $this->setNominal($nominal);
            $this->setRate($rate);
            $this->setRateDate($rateDate);
            $this->setSource($source);

        $validator = Validation::createValidatorBuilder()
            ->enableAnnotationMapping(true)
            ->addDefaultDoctrineAnnotationReader()
            ->getValidator();

        $violation = $validator->validate($this);
        if (count($violation)) {
            throw new \InvalidArgumentException(ConstraintViolationListParser::getString($violation));
        }
    }
}

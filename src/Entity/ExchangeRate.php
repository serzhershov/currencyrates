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
     * @ORM\Column(type="string", length=3)
     */
    private $currency_iso_code;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $rate;

    /**
     * @ORM\Column(type="string", length=3)
     */
    private $base_currency;

    /**
     * @ORM\Column(type="integer")
     */
    private $nominal;

    /**
     * @ORM\Column(type="datetime")
     */
    private $rate_date;

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
    public function setId($id)
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
    public function setCreated($created)
    {
        $this->created = $created;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCurrencyIsoCode()
    {
        return $this->currency_iso_code;
    }

    /**
     * @param mixed $currency_iso_code
     * @return ExchangeRate
     */
    public function setCurrencyIsoCode($currency_iso_code)
    {
        $this->currency_iso_code = $currency_iso_code;
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
    public function setRate($rate)
    {
        $this->rate = $rate;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBaseCurrency()
    {
        return $this->base_currency;
    }

    /**
     * @param mixed $base_currency
     * @return ExchangeRate
     */
    public function setBaseCurrency($base_currency)
    {
        $this->base_currency = $base_currency;
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
    public function setNominal($nominal)
    {
        $this->nominal = $nominal;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRateDate()
    {
        return $this->rate_date;
    }

    /**
     * @param mixed $rate_date
     * @return ExchangeRate
     */
    public function setRateDate($rate_date)
    {
        $this->rate_date = $rate_date;
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
    public function setSource($source): ExchangeRate
    {
        $this->source = $source;
        return $this;
    }

    /**
     * @param string $base_currency
     * @param string $currency_iso_code
     * @param int $nominal
     * @param string $rate
     * @param \DateTime $rate_date
     * @param string $source
     */
    public function __construct(
        string $base_currency,
        string $currency_iso_code,
        int $nominal,
        string $rate,
        \DateTime $rate_date,
        string $source
    )
    {
            $this->setCreated(new \DateTime());
            $this->setBaseCurrency($base_currency);
            $this->setCurrencyIsoCode($currency_iso_code);
            $this->setNominal($nominal);
            $this->setRate($rate);
            $this->setRateDate($rate_date);
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

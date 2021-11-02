<?php

namespace App\Repository;

use App\Connector\CurrencyRates\CurrencyRates;
use App\Entity\ExchangeRate;
use App\Utility\Collection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ExchangeRate|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExchangeRate|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExchangeRate[]    findAll()
 * @method ExchangeRate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExchangeRateRepository extends ServiceEntityRepository
{
    /**
     * @var string
     */
    private $appSource;

    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExchangeRate::class);
    }

    /**
     * @param string $appSource
     */
    public function setAppSource(string $appSource)
    {
        $this->appSource = $appSource;
    }

    /**
     * @param string $currencyIsoCode
     * @return ExchangeRate|null
     */
    public function getLatestCurrencyRates(string $currencyIsoCode) : ?ExchangeRate
    {
        return $this->findOneBy(
            [
                'source' => $this->appSource,
                'currency_iso_code' => $currencyIsoCode
            ],
            [
                'rate_date' => 'ASC',
                'created' => 'ASC'
            ]
        );
    }

    /**
     * @return string|null
     */
    public function getBaseCurrencyIsoCode() : ?string
    {
        $result = null;
        $entity = $this->findOneBy(
            ['source' => $this->appSource],
            ['rate_date' => 'ASC', 'created' => 'ASC']
        );
        if ($entity) {
            $result = $entity->getBaseCurrency();
        }
        return $result;
    }

    /**
     * @param Collection|CurrencyRates[] $currencyRates
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function persistRatesCollection(Collection $currencyRates) : void
    {
        if (empty($currencyRates)) {
            return;
        }

        $em = $this->getEntityManager();
        foreach ($currencyRates as $rateData) {
            if ( ! $rateData instanceof CurrencyRates) {
                throw new \InvalidArgumentException();
            }
            $em->persist(new ExchangeRate(
                $rateData->getBaseCurrency(),
                $rateData->getCurrencyIsoCode(),
                $rateData->getNominal(),
                $rateData->getRate(),
                $rateData->getRateDate(),
                $rateData->getSource()
            ));
        }
        $em->flush();
    }
}

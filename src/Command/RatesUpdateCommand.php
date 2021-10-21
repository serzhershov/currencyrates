<?php

namespace App\Command;


use App\Connector\CurrencyRates\CurrencyRates;
use App\Connector\CurrencyRates\RatesResolver;
use App\Controller\Service\CurrencyRatesController;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Validator\Validation;

class RatesUpdateCommand extends Command
{
    protected static $defaultName = 'app:rates-update';
    protected static $defaultDescription = 'Update rates database with connector data';

    /**
     * @var RatesResolver
     */
    private $ratesRetrievalConnector;
    /**
     * @var Validation
     */
    private $validator;
    /**
     * @var CurrencyRatesController
     */
    private $currencyRatesController;

    /**
     * @param RatesResolver $ratesRetrieval\
     */
    public function __construct(RatesResolver $ratesRetrieval, CurrencyRatesController $currencyRatesController)
    {
        $this->ratesRetrievalConnector = $ratesRetrieval;
        $this->currencyRatesController = $currencyRatesController;
        $this->validator = Validation::createValidator();
        parent::__construct();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        //@todo move validation to dto creation stage
        $ratesCollection = $this->ratesRetrievalConnector->getRatesCollection();
        $violations = [];
        $ratesCollection->each(function($key, $rate) use (&$violations) {
            /** @var CurrencyRates $rate */
            foreach (CurrencyRates::getPropertiesList() as $property) {
                $violations[] = $this->validator->validateProperty($rate, $property);
            }
            array_walk($violations, function($violation) {
                /** @var \Symfony\Component\Validator\ConstraintViolationList $violation */
                if ($violation->count()) {
                    //@todo: catch exception and return command::fail result
                    throw new \RuntimeException($violation);
                }
            });
            //@todo move saving to repository controller
            $this->currencyRatesController->saveCurrencyRatesByDto($rate);
        });

        return Command::SUCCESS;
    }
}

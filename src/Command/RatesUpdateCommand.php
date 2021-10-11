<?php

namespace App\Command;


use App\Connector\CurrencyRates\CurrencyRatesDTO;
use App\Connector\CurrencyRates\RatesRetrieval;
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
     * @var RatesRetrieval
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
     * @param RatesRetrieval $ratesRetrieval\
     */
    public function __construct(RatesRetrieval $ratesRetrieval, CurrencyRatesController $currencyRatesController)
    {
        $this->ratesRetrievalConnector = $ratesRetrieval;
        $this->currencyRatesController = $currencyRatesController;
        $this->validator = Validation::createValidator();
        parent::__construct();
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $ratesCollection = $this->ratesRetrievalConnector->getRatesCollection();
        $violations = [];
        $ratesCollection->each(function($key, $rate) use (&$violations) {
            /** @var CurrencyRatesDTO $rate */
            foreach (CurrencyRatesDTO::getPropertiesList() as $property) {
                $violations[] = $this->validator->validateProperty($rate, $property);
            }
            array_walk($violations, function($violation) {
                /** @var \Symfony\Component\Validator\ConstraintViolationList $violation */
                if ($violation->count()) {
                    throw new \RuntimeException($violation);
                }
            });
            $this->currencyRatesController->saveCurrencyRatesByDto($rate);
        });

        return Command::SUCCESS;
    }
}

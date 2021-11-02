<?php

namespace App\Command;

use App\Repository\ExchangeRateRepository;
use App\Connector\CurrencyRates\RatesResolver;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RatesUpdateCommand extends Command
{
    protected static $defaultName = 'app:rates-update';
    protected static $defaultDescription = 'Update rates database with connector data';

    /**
     * @var RatesResolver
     */
    private $ratesRetrievalConnector;
    /**
     * @var ExchangeRateRepository
     */
    private $exchangeRateRepository;

    /**
     * @param RatesResolver $ratesRetrieval
     * @param ExchangeRateRepository $exchangeRateRepository
     */
    public function __construct(
        RatesResolver $ratesRetrieval,
        ExchangeRateRepository $exchangeRateRepository
    ) {
        $this->ratesRetrievalConnector = $ratesRetrieval;
        $this->exchangeRateRepository = $exchangeRateRepository;
        parent::__construct();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln("0. starting exchange rate import from: " . $this->ratesRetrievalConnector->getSource());
        try {
            $ratesCollection = $this->ratesRetrievalConnector->getRatesCollection();
            $output->writeln("1. rates data acquired");
            $this->exchangeRateRepository->persistRatesCollection($ratesCollection);
            $output->writeln("2. rates data saved");
        } catch (\Exception $e) {
            $output->writeln("E: failed to import rates: ");
            $output->writeln($e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}

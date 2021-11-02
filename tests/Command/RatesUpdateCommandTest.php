<?php

namespace App\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Application;
use App\Command\RatesUpdateCommand;
use App\Repository\ExchangeRateRepository;

final class RatesUpdateCommandTest extends KernelTestCase
{
    public function testConfiguredRatesImportSuccessfully(): void
    {
        self::bootKernel();
        $container = static::getContainer();


        $application = new Application('echo', '1.0.0');
        $command = $container->get(RatesUpdateCommand::class);
        $ratesRepository = $container->get(ExchangeRateRepository::class);
        $this->assertCount(0, $ratesRepository->findAll());

        $application->add($command);

        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName()]);

        $result = $commandTester->getDisplay(true);
        dump($result);
        $this->assertStringContainsString('0. starting exchange rate import from:', $result);
        $this->assertStringContainsString('1. rates data acquired', $result);
        $this->assertStringContainsString('2. rates data saved', $result);
        $this->assertNotCount(0, $ratesRepository->findAll());

    }


}
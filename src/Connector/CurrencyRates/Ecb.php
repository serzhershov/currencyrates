<?php

namespace App\Connector\CurrencyRates;

use App\Connector\AbstractConnector;
use App\Utility\Collection;
use Symfony\Component\DomCrawler\Crawler;

class Ecb extends AbstractConnector implements RatesRetrieval
{
    protected $targetUri = 'https://www.ecb.europa.eu';
    protected $baseCurrency = 'EUR';

    /**
     * @inheritDoc
     */
    public function getRatesCollection(): Collection
    {
        $response = $this->getResponse('/stats/eurofxref/eurofxref-daily.xml');

        $doc = new \DOMDocument();
        $doc->preserveWhiteSpace = false;
        $doc->loadXML($response->getBody()->getContents());

        $attributes = [];
        $node1 = $doc->getElementsByTagName('Cube')->item(0);
        foreach ($node1->childNodes as $node2) {
            foreach ($node2->childNodes as $node3) {
                $attributes[] = new CurrencyRatesDTO(
                    (new \ReflectionClass($this))->getShortName(),
                    $node3->getAttribute('currency'),
                    $this->baseCurrency,
                    $node3->getAttribute('rate'),
                    1,
                    new \DateTime($node2->getAttribute('time'))
                );
            }
        }

        return new Collection($attributes);
    }
}
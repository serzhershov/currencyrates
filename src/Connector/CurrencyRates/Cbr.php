<?php

namespace App\Connector\CurrencyRates;

use App\Connector\HttpConnector;
use App\Utility\Collection;
use Symfony\Component\DomCrawler\Crawler;

final class Cbr extends HttpConnector implements RatesResolver
{
    protected string $targetUri = 'https://www.cbr.ru';
    protected string $baseCurrency = 'RUB';

    private array $filteredRates = ['XDR'];

    /**
     * @inheritDoc
     */
    public function getRatesCollection(): Collection
    {
        $response = $this->getResponse('/scripts/XML_daily.asp');
        $crawler = new Crawler($response->getBody()->getContents());
        $cbrDate = $crawler->filterXPath('//ValCurs')->extract(['Date']);
        $cbrDate = new \DateTime(array_pop($cbrDate));
        $attributes = $crawler
            ->filterXpath('//Valute')
            ->each(function($node, $i) use ($cbrDate) {
                $res = [];
                /** @var \Symfony\Component\DomCrawler\Crawler $node*/
                foreach($node->children() as $child) {
                    $res[$child->nodeName] = $child->nodeValue;
                }
                if (in_array(mb_strtoupper($res['CharCode']), $this->filteredRates, true)) {
                    return null;
                }
                return new CurrencyRates(
                    (new \ReflectionClass($this))->getShortName(),
                    $res['CharCode'],
                    $this->baseCurrency,
                    str_replace(array(',', ' '), array('.', ''), $res['Value']),
                    $res['Nominal'],
                    $cbrDate
                );
            });

        return new Collection(array_filter($attributes));
    }

    public function getSource(): string
    {
        return 'cbr';
    }
}
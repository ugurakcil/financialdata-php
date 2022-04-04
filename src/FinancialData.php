<?php
declare(strict_types=1);
namespace App;

use Scheb\YahooFinanceApi\ApiClient;
use Scheb\YahooFinanceApi\ApiClientFactory;

class FinancialData
{
    private $client;

    public function __construct()
    {
        $this->client = ApiClientFactory::createApiClient();
    }

    private function generateListId(array $list): string {
        return substr(base64_encode(implode('',$list)),0,20);
    }

    public function getQuotes(array $list): object
    {
        $apcuId = $this->generateListId($list);

        if(apcu_fetch($apcuId)) {
            return apcu_fetch($apcuId);
        } else {
            apcu_add($apcuId, $this->setQuotes($list), 10);
            return $this->setQuotes($list);
        }
    }

    private function setQuotes(array $list): object
    {
        $quotes = $this->client->getQuotes(array_keys($list));
        $output = (object)[];

        foreach($quotes as $quote) {
            $quoteDirection = "up";

            if(floatval($quote->getRegularMarketChangePercent()) < 0) {
                $quoteDirection = "down";
            }

            $output->{$quote->getSymbol()} = $this->outFormat([
                "name"      => $list[$quote->getSymbol()],
                "change"    => $quote->getRegularMarketChangePercent(),
                "price"     => $quote->getRegularMarketPrice(),
                "direction" => $quoteDirection
            ]);
        }

        return (object) $output;
    }

    public function outFormat(array $pureData) 
    {
        return (object) [
            "name"      => $pureData['name'],
            "change"    => number_format((float) $pureData['change'], 2, '.', ''),
            "price"     => (float) number_format($pureData['price'],  2, '.', ''),
            "direction" => $pureData['direction'] 
        ];
    }

}
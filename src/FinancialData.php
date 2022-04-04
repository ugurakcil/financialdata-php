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

            $output->{$quote->getSymbol()} = (object) [
                "name"      => $list[$quote->getSymbol()],
                "change"    => number_format($quote->getRegularMarketChangePercent(),2),
                "price"     => number_format($quote->getRegularMarketPrice(), 2),
                "direction" => $quoteDirection
            ];
        }

        return (object) $output;
    }

}
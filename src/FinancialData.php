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

    public function getQuotes(array $list): object
    {
        /*
        if(apc_fetch("quotes")) {
            echo "exists";
            return apc_fetch("quotes");
        } else {
            echo "newdata";
            apc_add("quotes", $this->setQuotes($list), 10);
            return $this->setQuotes($list);
        }
        */
    }

    public function setQuotes(array $list): object
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
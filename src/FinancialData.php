<?php
/**
 * Template Name: Authors
 *
 * @package      Financial Data PHP
 * @since        1.0.0
 * @link         https://github.com/ugurakcil/financialdata-php
 * @author       Uğur AKÇIL <ugurakcil@gmail.com>
 * @copyright    Copyright (c) 2022, Uğur AKÇIL
 * @license      https://github.com/ugurakcil/financialdata-php/blob/master/licence.md MIT License
 *
 */

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

    /*
    * Generates a special id for caching using the array's information
    * */
    private function generateListId(array $list): string {
        return substr(base64_encode(implode('',$list)),0,20);
    }

    /*
    * It returns the instruments requested with Array 
    * by checking whether they are in the cache or not
    * */
    public function getQuotes(array $list): object
    {
        $apcuId = $this->generateListId($list);

        if(apcu_fetch($apcuId)) {
            return apcu_fetch($apcuId);
        } else {
            apcu_add($apcuId, $this->setQuotes($list), 3600);
            return $this->setQuotes($list);
        }
    }

    /*
    * Makes and arranges the desired instruments into an object
    * */
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

    /*
    * Used to apply filters such as type conversion 
    * and number format of the information of financial instruments
    * */
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
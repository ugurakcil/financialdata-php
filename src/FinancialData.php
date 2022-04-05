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
        return substr(base64_encode(implode('',$list)), 0, 20);
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
            apcu_add($apcuId, $this->setQuotes($list), 4);
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
            /*
            * It is recorded whether there has been a decrease 
            * or an increase from the market closing so far
            * */
            $quoteDirection = "up";

            if(floatval($quote->getRegularMarketChangePercent()) < 0) {
                $quoteDirection = "down";
            }
            
            /*
            * Fixed the bug caused by yahoo 
            * with instant change of symbol names in exchange rates
            * */
            if(!isset($list[$quote->getSymbol()]) && substr($quote->getSymbol(), 0, 3) == 'USD') {
                $list[$quote->getSymbol()] = $list[substr($quote->getSymbol(),3)];
            }
            
            if(!isset($list[$quote->getSymbol()]) && substr($quote->getSymbol(), 0, 3) != 'USD') {
                $list[$quote->getSymbol()] = $list['USD'.$quote->getSymbol()];
            }
            
            /*
            * Outputs important data
            * */
            $output->{$quote->getSymbol()} = $this->numFormat([
                "name"      => $list[$quote->getSymbol()],
                "change"    => $quote->getRegularMarketChangePercent(),
                "price"     => $quote->getRegularMarketPrice(),
                "open"      => $quote->getRegularMarketOpen(),
                "close"     => $quote->getRegularMarketPreviousClose(),
                "high"      => $quote->getRegularMarketDayHigh(),
                "low"       => $quote->getRegularMarketDayLow(),
                "direction" => $quoteDirection
            ]);
        }

        return (object) $output;
    }

    /*
    * Used to apply filters such as type conversion 
    * and number format of the information of financial instruments
    * */
    public function numFormat(array $pureData): object
    {
        /*
        * A null value is assigned 
        * if missing data in custom formatting is sent
        * */
        if(!isset($pureData['name'])) {
            $pureData['name'] = null;
        }

        if(!isset($pureData['change'])) {
            $pureData['change'] = null;
        }

        if(!isset($pureData['price'])) {
            $pureData['price'] = null;
        }

        if(!isset($pureData['open'])) {
            $pureData['open'] = null;
        }

        if(!isset($pureData['close'])) {
            $pureData['close'] = null;
        }

        if(!isset($pureData['high'])) {
            $pureData['high'] = null;
        }

        if(!isset($pureData['low'])) {
            $pureData['low'] = null;
        }

        if(!isset($pureData['direction'])) {
            $pureData['direction'] = null;
        }

        /*
        * Formatting the data to be output
        * */
        return (object) [
            "name"      => $pureData['name'],
            "change"    => number_format((float) $pureData['change'], 2, '.', ''),
            "price"     => (float) number_format((float) $pureData['price'],  2, '.', ''),
            "open"      => (float) number_format((float) $pureData['open'],  2, '.', ''),
            "close"     => (float) number_format((float) $pureData['close'],  2, '.', ''),
            "high"      => (float) number_format((float) $pureData['high'],  2, '.', ''),
            "low"       => (float) number_format((float) $pureData['low'],  2, '.', ''),
            "direction" => $pureData['direction'] 
        ];
    }

    /*
    * Normally the daily change rate comes from the api 
    * but this can be used to calculate a custom change
    * */
    public function calculateChange(float $v1, float $v2): float
    {
        return ($v1 - $v2) / (($v1 + $v2) / 2) * 100;
    }

    /*
    * Selects what will be displayed from the object to be output
    * */
    public function decoratePrint(object $list, array $shown): object 
    {
        return (object) array_map(function($row) use ($shown) {
            $output = (object) [];

            foreach($shown as $shownRow) {
                if(isset($row->{$shownRow})) {
                    $output->{$shownRow} = $row->{$shownRow};
                }
            }

            return $output;
        }, get_object_vars($list));
    }

}
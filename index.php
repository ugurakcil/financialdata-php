<?php
declare(strict_types=1);

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/vendor/autoload.php';

//apcu_clear_cache();

$financialData = new \App\FinancialData();

$currencies = $financialData->getQuotes([
    "EURUSD=X" => "EUR / USD",
    "GBPUSD=X" => "GBP / USD",
    "USDJPY=X" => "USD / JPY",
    "XAUUSD=X" => "XAU / USD",
    "XAGUSD=X" => "XAG / USD"
]);

$stocks = $financialData->getQuotes([
    "AEFES.IS"  => "AEFES",
    "AGHOL.IS"  => "AGHOL",
    "AKBNK.IS"  => "AKBNK",
    "AKSA.IS"   => "AKSA",
    "AKSEN.IS"  => "AKSEN",
    "ALARK.IS"  => "ALARK",
    "ALGYO.IS"  => "ALGYO",
    "ALKIM.IS"  => "ALKIM"
]);

?>

<style type="text/css">
    /* example style - will be remove */
    .line{ 
        display: flex;
        margin-bottom:20px;
    }
    .item{
        flex: 1;
        border: 1px solid cadetblue;
        padding:5px;
    }
    .line-first{
        background-color:antiquewhite;
    }
    .line-second{
        background-color:azure;
    }
</style>

<div class="line line-first">
<?php foreach($currencies as $currency): ?>
<div class="item <?php echo $currency->direction; ?>">
    <div><?php echo $currency->name; ?></div>
    <div><?php echo $currency->change; ?>%</div>
    <div><?php echo $currency->price; ?></div>
</div>
<?php endforeach; ?>
</div>

<div class="line line-second">
<?php foreach($stocks as $stock): ?>
<div class="item <?php echo $stock->direction; ?>">
    <div><?php echo $stock->name; ?></div>
    <div><?php echo $stock->change; ?>%</div>
    <div><?php echo $stock->price; ?></div>
</div>
<?php endforeach; ?>
</div>
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

/*
* Configurations
* All requests are allowed in development mode
* Don't forget to set the development mode to false when going live
* */
$developmentMode = true;

$allowedOrigins = array(
    '(http(s)://)?(www\.)?website\.xyz',
    '(http(s)://)?(www\.)?test\.my',
    '(http(s)://)?(www\.)?my\-domain\.com'
);

/*
* Header information, debugging, dependencies
* */
if($developmentMode) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    apcu_clear_cache();
    header('Access-Control-Allow-Origin: *');
}

header('Content-type: application/json; charset=utf-8');

if (!$developmentMode && isset($_SERVER['HTTP_ORIGIN']) && $_SERVER['HTTP_ORIGIN'] != '') {
    foreach ($allowedOrigins as $allowedOrigin) {
        if (preg_match('#' . $allowedOrigin . '#', $_SERVER['HTTP_ORIGIN'])) {
            header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
            header('Access-Control-Allow-Methods: GET'); // PUT, POST, OPTIONS, DELETE
            break;
        }
    }
}

require __DIR__ . '/vendor/autoload.php';

/*
* The requested assets to the class are requested in two different parts
* */
$financialData = new \App\FinancialData();

$currencies = $financialData->getQuotes([
    "EURUSD=X"  => "EUR / USD",
    "GBPUSD=X"  => "GBP / USD",
    "TRY=X"     => "USD / TRY",
    "EURTRY=X"  => "EUR / TRY",
    "KGRUSD"    => "KGR / USD",
    "JPY=X"     => "USD / JPY",
    "GC=F"      => "XAU / USD",
    "SI=F"      => "XAG / USD",
    "BZ=F"      => "XBR / USD",
    "MCL=F"     => "XTI / USD",
]);

/*
* A specially calculated financial instrument can be added with an outformat
* */
$currencies->{"KGRUSD"} = $financialData->numFormat([
    "name"      => "KGR / USD",
    "change"    => $currencies->{"GC=F"}->change,
    "direction" => $currencies->{"GC=F"}->direction,
    "close"     => $currencies->{"GC=F"}->close / 31,1034768,
    "price"     => $currencies->{"GC=F"}->price / 31,1034768
]);

$kgrTryChange = $financialData->calculateChange(
    $currencies->{"KGRUSD"}->close * $currencies->{"TRY=X"}->close,
    $currencies->{"KGRUSD"}->price * $currencies->{"TRY=X"}->price
);

$currencies->{"KGRTRY"} = $financialData->numFormat([
    "name"      => "KGR / TRY",
    "change"    => $kgrTryChange,
    "direction" => $kgrTryChange < 0 ? 'down' : 'up',
    "close"     => $currencies->{"KGRUSD"}->close * $currencies->{"TRY=X"}->close,
    "price"     => $currencies->{"KGRUSD"}->price * $currencies->{"TRY=X"}->price
]);

$stocks = $financialData->getQuotes([
    "AEFES.IS"  => "AEFES",
    "AGHOL.IS"  => "AGHOL",
    "AKBNK.IS"  => "AKBNK",
    "AKSA.IS"   => "AKSA",
    "AKSEN.IS"  => "AKSEN",
    "ALARK.IS"  => "ALARK",
    "ALGYO.IS"  => "ALGYO",
    "ALKIM.IS"  => "ALKIM",
    "ARCLK.IS"  => "ARCLK",
    "ARDYZ.IS"  => "ARDYZ",
    "ASELS.IS"  => "ASELS",
    "AYDEM.IS"  => "AYDEM",
    "BAGFS.IS"  => "BAGFS",
    "BASGZ.IS"  => "BASGZ",
    "BERA.IS"   => "BERA",
    "BIMAS.IS"  => "BIMAS",
    "BIOEN.IS"  => "BIOEN",
    "BRISA.IS"  => "BRISA",
    "BRMEN.IS"  => "BRMEN",
    "BRYAT.IS"  => "BRYAT",
    "CANTE.IS"  => "CANTE",
    "CCOLA.IS"  => "CCOLA",
    "CIMSA.IS"  => "CIMSA",
    "CMENT.IS"  => "CMENT",
    "DENGE.IS"  => "DENGE",
    "DEVA.IS"   => "DEVA",
    "DOAS.IS"   => "DOAS",
    "DOHOL.IS"  => "DOHOL",
    "ECILC.IS"  => "ECILC",
    "EGEEN.IS"  => "EGEEN",
    "EKGYO.IS"  => "EKGYO",
    "ENJSA.IS"  => "ENJSA",
    "ENKAI.IS"  => "ENKAI",
    "ERBOS.IS"  => "ERBOS",
    "EREGL.IS"  => "EREGL",
    "ESEN.IS"   => "ESEN",
    "ETILR.IS"  => "ETILR",
    "FROTO.IS"  => "FROTO",
    "GARAN.IS"  => "GARAN",
    "GENIL.IS"  => "GENIL",
    "GLYHO.IS"  => "GLYHO",
    "GOZDE.IS"  => "GOZDE",
    "GSDHO.IS"  => "GSDHO",
    "GUBRF.IS"  => "GUBRF",
    "GWIND.IS"  => "GWIND",
    "HALKB.IS"  => "HALKB",
    "HEKTS.IS"  => "HEKTS",
    "INDES.IS"  => "INDES",
    "IPEKE.IS"  => "IPEKE",
    "ISBIR.IS"  => "ISBIR",
    "ISCTR.IS"  => "ISCTR",
    "ISDMR.IS"  => "ISDMR",
    "ISFIN.IS"  => "ISFIN",
    "ISGYO.IS"  => "ISGYO",
    "ISMEN.IS"  => "ISMEN",
    "ISYAT.IS"  => "ISYAT",
    "JANTS.IS"  => "JANTS",
    "KARSN.IS"  => "KARSN",
    "KARTN.IS"  => "KARTN",
    "KCHOL.IS"  => "KCHOL",
    "KENT.IS"   => "KENT",
    "KLNMA.IS"  => "KLNMA",
    "KONTR.IS"  => "KONTR",
    "KORDS.IS"  => "KORDS",
    "KOZAA.IS"  => "KOZAA",
    "KOZAL.IS"  => "KOZAL",
    "KRDMD.IS"  => "KRDMD",
    "KUTPO.IS"  => "KUTPO",
    "KZBGY.IS"  => "KZBGY",
    "LOGO.IS"   => "LOGO",
    "MAVI.IS"   => "MAVI",
    "MGROS.IS"  => "MGROS",
    "NTHOL.IS"  => "NTHOL",
    "NUGYO.IS"  => "NUGYO",
    "ODAS.IS"   => "ODAS",
    "OTKAR.IS"  => "OTKAR",
    "OTTO.IS"   => "OTTO",
    "OYAKC.IS"  => "OYAKC",
    "PARSN.IS"  => "PARSN",
    "PETKM.IS"  => "PETKM",
    "PGSUS.IS"  => "PGSUS",
    "QNBFB.IS"  => "QNBFB",
    "QNBFL.IS"  => "QNBFL",
    "QUAGR.IS"  => "QUAGR",
    "SAHOL.IS"  => "SAHOL",
    "SASA.IS"   => "SASA",
    "SELEC.IS"  => "SELEC",
    "SISE.IS"   => "SISE",
    "SKBNK.IS"  => "SKBNK",
    "SNGYO.IS"  => "SNGYO",
    "SNKRN.IS"  => "SNKRN",
    "SOKM.IS"   => "SOKM",
    "TAVHL.IS"  => "TAVHL",
    "TBORG.IS"  => "TBORG",
    "TCELL.IS"  => "TCELL",
    "THYAO.IS"  => "THYAO",
    "TKFEN.IS"  => "TKFEN",
    "TKNSA.IS"  => "TKNSA",
    "TKURU.IS"  => "TKURU",
    "TOASO.IS"  => "TOASO",
    "TRGYO.IS"  => "TRGYO",
    "TRILC.IS"  => "TRILC",
    "TSKB.IS"   => "TSKB",
    "TTKOM.IS"  => "TTKOM",
    "TTRAK.IS"  => "TTRAK",
    "TUPRS.IS"  => "TUPRS",
    "TURSG.IS"  => "TURSG",
    "ULKER.IS"  => "ULKER",
    "UTPYA.IS"  => "UTPYA",
    "VAKBN.IS"  => "VAKBN",
    "VESBE.IS"  => "VESBE",
    "VESTL.IS"  => "VESTL",
    "YKBNK.IS"  => "YKBNK",
    "ZOREN.IS"  => "ZOREN",
    "ZRGYO.IS"  => "ZRGYO"
]);

/*
* Requests from two different parties are combined on a single json
* */
$shown = ['name', 'change', 'direction', 'price'];

$listAllFinancialData = (object) [
    'currencies'    => $financialData->decoratePrint($currencies, $shown),
    'stocks'        => $financialData->decoratePrint($stocks, $shown)
];

echo json_encode($listAllFinancialData);
<?php
require_once __DIR__ . "/../vendor/autoload.php";

use Silviooosilva\CacheerPhp\Cacheer;

$options = [
    "cacheDir" =>  __DIR__ . "/cache",
    "expirationTime" => "2 hour"
];

$Cacheer = new Cacheer($options);

// Dados a serem armazenados no cache
$cacheKey = 'daily_stats';
$dailyStats = [
    'visits' => 1500,
    'signups' => 35,
    'revenue' => 500.75,
];

// Armazenando dados no cache
$Cacheer->putCache($cacheKey, $dailyStats);

// Recuperando dados do cache por 2 horas
$cachedStats = $Cacheer->getCache($cacheKey);

if ($Cacheer->isSuccess()) {
    echo "Cache Found: ";
    print_r($cachedStats);
} else {
    echo $Cacheer->getMessage();
}

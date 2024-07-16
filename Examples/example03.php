<?php

require_once __DIR__ . "/../vendor/autoload.php";

use Silviooosilva\CacheerPhp\Cacheer;

$options = [
    "cacheDir" =>  __DIR__ . "/cache",
];

$Cacheer = new Cacheer($options);

// Chave do cache a ser limpo
$cacheKey = 'user_profile_123';

// Limpando um item específico do cache

$Cacheer->clearCache($cacheKey);

if ($Cacheer->isSuccess()) {
    echo $Cacheer->getMessage();
} else {
    echo $Cacheer->getMessage();
}

$Cacheer->flushCache();

if ($Cacheer->isSuccess()) {
    echo $Cacheer->getMessage();
} else {
    echo $Cacheer->getMessage();
}

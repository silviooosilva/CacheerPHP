<?php
require_once __DIR__ . "/../vendor/autoload.php";

use Silviooosilva\CacheerPhp\Cacheer;


$options = [
    "cacheDir" =>  __DIR__ . "/cache",
];

$Cacheer = new Cacheer($options);

// Dados a serem armazenados no cache
$cacheKey = 'user_profile_1234';
$userProfile = [
    'id' => 123,
    'name' => 'John Doe',
    'email' => 'john.doe@example.com',
];

// Armazenando dados no cache
$Cacheer->putCache($cacheKey, $userProfile);

// Recuperando dados do cache
$cachedProfile = $Cacheer->getCache($cacheKey);

if ($Cacheer->isSuccess()) {
    echo "Cache Found: ";
    print_r($cachedProfile);
} else {
    echo $Cacheer->getMessage();
}



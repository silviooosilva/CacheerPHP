<?php

use Silviooosilva\CacheerPhp\Cacheer;

require_once __DIR__ . "/../vendor/autoload.php";

$Cacheer = new Cacheer();
$Cacheer->setDriver()->useRedisDriver();

// Dados a serem armazenados no cache
$cacheKey = 'user_profile_01';
$userProfile = [
    'id' => 1,
    'name' => 'Sílvio Silva',
    'email' => 'gasparsilvio7@gmail.com',
];

// Armazenando dados no cache
$Cacheer->putCache($cacheKey, $userProfile, ttl: 300);

// Recuperando dados do cache
if($Cacheer->isSuccess()){
    echo "Cache Found: ";
    print_r($Cacheer->getCache($cacheKey));
} else {
  echo $Cacheer->getMessage();
}

// Renovando os dados do cache
$Cacheer->renewCache($cacheKey, 3600);

if($Cacheer->isSuccess()){
  echo $Cacheer->getMessage() . PHP_EOL;
} else {
  echo $Cacheer->getMessage() . PHP_EOL;

}
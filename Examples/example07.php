<?php

use Silviooosilva\CacheerPhp\Cacheer;

require_once __DIR__ . "/../vendor/autoload.php";

$Cacheer = new Cacheer();
$Cacheer->setDriver()->useRedisDriver();

// Dados a serem armazenados no cache
$cacheKey = 'user_profile_1';
$userProfile = [
    'id' => 1,
    'name' => 'Sílvio Silva',
    'email' => 'gasparsilvio7@gmail.com',
];

$userProfile02 = [
    'casaNº' => 2130,
    'telefone' => "(999)999-9999"
];


// Armazenando dados no cache
$Cacheer->putCache($cacheKey, $userProfile);

// Recuperando dados do cache
if($Cacheer->isSuccess()){
    echo "Cache Found: ";
    print_r($Cacheer->getCache($cacheKey));
} else {
  echo $Cacheer->getMessage();
}


// Mesclando os dados
$Cacheer->appendCache($cacheKey, $userProfile02);

if($Cacheer->isSuccess()){
    echo $Cacheer->getMessage() . PHP_EOL;
    print_r($Cacheer->getCache($cacheKey));
} else {
  echo $Cacheer->getMessage();
}


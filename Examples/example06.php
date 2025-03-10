<?php

use Silviooosilva\CacheerPhp\Cacheer;

require_once __DIR__ . "/../vendor/autoload.php";

$Cacheer = new Cacheer();
$Cacheer->setDriver()->useRedisDriver();

// Dados a serem armazenados no cache
$cacheKey = 'user_profile_1234';
$userProfile = [
    'id' => 1,
    'name' => 'Sílvio Silva',
    'email' => 'gasparsilvio7@gmail.com',
    'role' => 'Developer'
];
$cacheNamespace = 'userData';

// Armazenando dados no cache
//$Cacheer->putCache($cacheKey, $userProfile, $cacheNamespace);

$Cacheer->has($cacheKey, $cacheNamespace);

// Verificando se o cache existe e recuperando os dados
if ($Cacheer->isSuccess()) {
    $cachedProfile = $Cacheer->getCache($cacheKey, $cacheNamespace);
    echo "Perfil de Usuário Encontrado:\n";
    print_r($cachedProfile);
} else {
    echo "Cache não encontrado: " . $Cacheer->getMessage();
}


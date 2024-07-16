<?php

require_once __DIR__ . "/../vendor/autoload.php";

use Silviooosilva\CacheerPhp\Cacheer;

$options = [
    "cacheDir" =>  __DIR__ . "/cache",
];

$Cacheer = new Cacheer($options);

// URL da API e chave de cache
$apiUrl = 'https://jsonplaceholder.typicode.com/posts';
$cacheKey = 'api_response_' . md5($apiUrl);

// Verificando se a resposta da API já está no cache
$cachedResponse = $Cacheer->getCache($cacheKey);

if ($Cacheer->isSuccess()) {
    // Use a resposta do cache
    $response = $cachedResponse;
} else {
    // Faça a chamada à API e armazene a resposta no cache
    $response = file_get_contents($apiUrl);
    $Cacheer->putCache($cacheKey, $response);
}

// Usando a resposta da API (do cache ou da chamada)
$data = json_decode($response, true);
print_r($data);

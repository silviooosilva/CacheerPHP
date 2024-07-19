# Cacheer-PHP

[![Maintainer](https://img.shields.io/badge/maintainer-@silviooosilva-blue.svg?style=flat-square)](https://twitter.com/silviooosilva)
[![Source Code](http://img.shields.io/badge/source-silviooosilva/CacheerPHP-blue.svg?style=flat-square)](https://github.com/silviooosilva/CacheerPHP)
[![PHP from Packagist](https://img.shields.io/packagist/php-v/silviooosilva/CacheerPHP.svg?style=flat-square)](https://packagist.org/packages/silviooosilva/cacheer-php)
[![Latest Version](https://img.shields.io/github/release/silviooosilva/CacheerPHP.svg?style=flat-square)](https://github.com/silviooosilva/CacheerPHP/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Quality Score](https://img.shields.io/scrutinizer/g/silviooosilva/CacheerPHP.svg?style=flat-square)](https://scrutinizer-ci.com/g/silviooosilva/CacheerPHP)
[![Total Downloads](https://img.shields.io/packagist/dt/ssilviooosilva/CacheerPHP.svg?style=flat-square)](https://packagist.org/packages/silviooosilva/cacheer-php)


Cacheer-PHP é um pacote minimalista para caching em PHP, oferecendo uma interface simples para armazenar e recuperar dados em cache utilizando arquivos.

## Funcionalidades

- Armazenamento e recuperação de cache em arquivos.
- Expiração de cache personalizável.
- Limpeza e flush de cache.
- Suporte a namespaces para organização de cache.

## Instalação

1. Clone o repositório ou faça o download dos arquivos:

    ```sh
    git clone https://github.com/silviooosilva/CacheerPHP.git
    ```

2. Inclua o autoload do Composer no seu projeto


## Uso

<p>Exemplo 01 </p>
<p>Cache de Dados Simples</p>

```php
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



```

<p>Exemplo 02 </p>
<p>Cache com tempo de expiração Personalizado</p>

```php
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

```

<p>Exemplo 03 </p>
<p>Limpeza e Flush do Cache</p>

```php
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


```

<p>Exemplo 04 </p>
<p>Uso de Namespaces</p>

```php
<?php
require_once __DIR__ . "/../vendor/autoload.php";

use Silviooosilva\CacheerPhp\Cacheer;

$options = [
    "cacheDir" =>  __DIR__ . "/cache",
];

$Cacheer = new Cacheer($options);

// Dados a serem armazenados no cache com namespace
$namespace = 'session_data_01';
$cacheKey = 'session_456';
$sessionData = [
    'user_id' => 456,
    'login_time' => time(),
];

// Armazenando dados no cache com namespace
$Cacheer->putCache($cacheKey, $sessionData, $namespace);

// Recuperando dados do cache
$cachedSessionData = $Cacheer->getCache($cacheKey, $namespace);

if ($Cacheer->isSuccess()) {
    echo "Cache Found: ";
    print_r($cachedSessionData);
} else {
    echo $Cacheer->getMessage();
}


```

<p>Exemplo 05 </p>
<p>Cache de Resposta de API</p>

```php
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

```

### Configuração

Para utilizar o Cacheer-PHP, você precisa configurar o diretório de cache:

```sh
$options = [
    'cacheDir' => __DIR__ . '/cache'
];
```
Opcionalmente, você pode configurar o tempo de expiração do cache:

```sh
$options = [
    'cacheDir' => __DIR__ . '/cache',
    'expirationTime' => '1 hour(s)' // String
];
```

Pode configurar o tempo de expiração do cache em: 
```php
Minutos -> minute(s)
Horas -> hour(s)
Segundos -> second(s)
```

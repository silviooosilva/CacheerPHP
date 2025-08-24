w## Exemplo 02

<p>Cache com tempo de expiração Personalizado</p>

```php
<?php
require_once __DIR__ . "/../vendor/autoload.php";

use Silviooosilva\CacheerPhp\Cacheer;

$options = [
    "cacheDir" =>  __DIR__ . "/cache",
    "expirationTime" => "2 hour" //Primeira opção (definição global)
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
$cachedStats = $Cacheer->getCache($cacheKey, 'namespace', '2 hours'); //Segunda opção (definição no método)

if ($Cacheer->has($cacheKey, 'namespace')) {
    echo "Cache Found: ";
    print_r($cachedStats);
} else {
    echo $Cacheer->getMessage();
}

// Ou utilizando isSuccess()
$Cacheer->has($cacheKey, 'namespace');
if ($Cacheer->isSuccess()) {
    echo "Cache Found: ";
    print_r($cachedStats);
}

```

Pode configurar o tempo de expiração do cache em:

```php
Minutos: minute(s)
Horas: hour(s)
Segundos: second(s)
```

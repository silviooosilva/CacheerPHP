## Exemplo 04
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

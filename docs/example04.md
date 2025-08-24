## Exemplo 04
<p>Use of Namespaces</p>

```php
<?php
require_once __DIR__ . "/../vendor/autoload.php";

use Silviooosilva\CacheerPhp\Cacheer;

$options = [
    "cacheDir" =>  __DIR__ . "/cache",
];

$Cacheer = new Cacheer($options);

// Data to be stored in the namespace cache
$namespace = 'session_data_01';
$cacheKey = 'session_456';
$sessionData = [
    'user_id' => 456,
    'login_time' => time(),
];

// Static call example
Cacheer::putCache($cacheKey, $sessionData, $namespace);

// Caching data with namespace
$Cacheer->putCache($cacheKey, $sessionData, $namespace);

// Retrieving data from the cache
$cachedSessionData = $Cacheer->getCache($cacheKey, $namespace);

if ($Cacheer->has($cacheKey, $namespace)) {
    echo "Cache Found: ";
    print_r($cachedSessionData);
} else {
    echo $Cacheer->getMessage();
}

// Alternativamente
$Cacheer->has($cacheKey, $namespace);
if ($Cacheer->isSuccess()) {
    echo "Cache Found: ";
    print_r($cachedSessionData);
}
```
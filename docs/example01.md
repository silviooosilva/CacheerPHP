## Exemplo 01
<p>Simple Data Cache</p>

```php
<?php
require_once __DIR__ . "/../vendor/autoload.php";

use Silviooosilva\CacheerPhp\Cacheer;


$options = [
    "cacheDir" =>  __DIR__ . "/cache",
];

$Cacheer = new Cacheer($options);

// Data to be stored in the cache
$cacheKey = 'user_profile_1234';
$userProfile = [
    'id' => 123,
    'name' => 'John Doe',
    'email' => 'john.doe@example.com',
];

// Static call example
Cacheer::putCache($cacheKey, $userProfile);

// Storing data in the cache
$Cacheer->putCache($cacheKey, $userProfile);

// Retrieving data from the cache
$cachedProfile = $Cacheer->getCache($cacheKey);

if ($Cacheer->has($cacheKey)) {
    echo "Cache Found: ";
    print_r($cachedProfile);
} else {
    echo $Cacheer->getMessage();
}

// Alternatively, using the previous style
$Cacheer->has($cacheKey);
if ($Cacheer->isSuccess()) {
    echo "Cache Found: ";
    print_r($cachedProfile);
}

```

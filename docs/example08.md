## Exemplo 08

<p>String Data Output</p>
To configure the output type you want, you have to enable it when you instantiate the class, passing the last parameter as true.:

```php

<?php

require_once  __DIR__  .  "/../vendor/autoload.php";
use Silviooosilva\CacheerPhp\Cacheer;

$options = [
"cacheDir" => __DIR__  .  "/cache",
];

$Cacheer = new  Cacheer($options, $formatted = true); // True last parameter

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

// Retrieving data from the cache in JSON format

$cachedProfile = $Cacheer->getCache(
$cacheKey,
$namespace,
$ttl)->toString();

if ($Cacheer->has($cacheKey)) {
echo  "Cache Found: ";
print_r($cachedProfile);
} else {
echo  $Cacheer->getMessage();
}

// Ou utilizando isSuccess()
$Cacheer->has($cacheKey);
if ($Cacheer->isSuccess()) {
echo  "Cache Found: ";
print_r($cachedProfile);
}

```
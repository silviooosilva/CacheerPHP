## Exemplo 09

<p>Automatic cleaning of the cache directory (flushAfter)</p>

To use automatic cleaning of the cache directory, you will need to configure the options:

```php

<?php

require_once  __DIR__  .  "/../vendor/autoload.php";
use Silviooosilva\CacheerPhp\Cacheer;

$options = [
"cacheDir" => __DIR__  .  "/cache",
"flushAfter" => "1 week" //string
];

$Cacheer = new  Cacheer($options);

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
The accepted time formats for `flushAfter` are:

```php
Segundos: second(s)
Minutos: minute(s)
Horas: hour(s)
Dias: day(s)
Semanas: week(s)
Meses: month(s)
Anos: year(s)

```

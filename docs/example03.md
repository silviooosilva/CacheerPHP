## Exemplo 03
<p>Cache cleaning and flushing</p>

```php
<?php

require_once __DIR__ . "/../vendor/autoload.php";

use Silviooosilva\CacheerPhp\Cacheer;

$options = [
    "cacheDir" =>  __DIR__ . "/cache",
];

$Cacheer = new Cacheer($options);

// Key of the cache to be cleared
$cacheKey = 'user_profile_123';

// Clearing a specific item from the cache
if ($Cacheer->clearCache($cacheKey)) {
    echo $Cacheer->getMessage();
}

if ($Cacheer->flushCache()) {
    echo $Cacheer->getMessage();
}

// Utilizando isSuccess()
$Cacheer->clearCache($cacheKey);
if ($Cacheer->isSuccess()) {
    echo $Cacheer->getMessage();
}


```

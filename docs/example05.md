## Exemplo 05
<p>API Response Cache</p>

```php
<?php

require_once __DIR__ . "/../vendor/autoload.php";

use Silviooosilva\CacheerPhp\Cacheer;

$options = [
    "cacheDir" =>  __DIR__ . "/cache",
];

$Cacheer = new Cacheer($options);

// API URL and cache key
$apiUrl = 'https://jsonplaceholder.typicode.com/posts';
$cacheKey = 'api_response_' . md5($apiUrl);

// Checking if the API response is already in the cache
$cachedResponse = $Cacheer->getCache($cacheKey);

if ($Cacheer->isSuccess()) {
    // Use the cache response
    $response = $cachedResponse;
} else {
    // Call the API and store the response in the cache
    $response = file_get_contents($apiUrl);
    $Cacheer->putCache($cacheKey, $response);
}

// Using the API response (from cache or call)
$data = json_decode($response, true);
print_r($data);

```

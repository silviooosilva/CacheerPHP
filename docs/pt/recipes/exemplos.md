# Receitas (Exemplos)

Exemplos consolidados do CacheerPHP. Para exemplos executáveis em PHP, veja `Examples/`.

## Exemplo 01 — Cache Simples de Dados

```php
require_once __DIR__ . "/../vendor/autoload.php";

use Silviooosilva\CacheerPhp\Cacheer;

$options = [
    "cacheDir" =>  __DIR__ . "/cache",
];

$Cacheer = new Cacheer($options);

$cacheKey = 'user_profile_1234';
$userProfile = [
    'id' => 123,
    'name' => 'John Doe',
    'email' => 'john.doe@example.com',
];

Cacheer::putCache($cacheKey, $userProfile);
$Cacheer->putCache($cacheKey, $userProfile);

$cachedProfile = $Cacheer->getCache($cacheKey);

if ($Cacheer->has($cacheKey)) {
    var_dump($cachedProfile);
}
```

## Exemplo 02 — Tempo de Expiração Personalizado (TTL)

```php
require_once __DIR__ . "/../vendor/autoload.php";

use Silviooosilva\CacheerPhp\Cacheer;

$options = [
    "cacheDir" =>  __DIR__ . "/cache",
    "expirationTime" => "2 hours"
];

$Cacheer = new Cacheer($options);

$cacheKey = 'daily_stats';
$dailyStats = [
    'visits' => 1500,
    'signups' => 35,
    'revenue' => 500.75,
];

Cacheer::putCache($cacheKey, $dailyStats, 'namespace', '2 hours');
$Cacheer->putCache($cacheKey, $dailyStats);

$cachedStats = $Cacheer->getCache($cacheKey, 'namespace', '2 hours');
```

Formatos aceitos: seconds, minutes, hours.

## Exemplo 03 — Limpeza e Flush

```php
require_once __DIR__ . "/../vendor/autoload.php";

use Silviooosilva\CacheerPhp\Cacheer;

$options = [
    "cacheDir" =>  __DIR__ . "/cache",
];

$Cacheer = new Cacheer($options);

Cacheer::flushCache();

$cacheKey = 'user_profile_123';
if ($Cacheer->clearCache($cacheKey)) {
    echo $Cacheer->getMessage();
}

if ($Cacheer->flushCache()) {
    echo $Cacheer->getMessage();
}
```

## Exemplo 04 — Namespaces

```php
require_once __DIR__ . "/../vendor/autoload.php";

use Silviooosilva\CacheerPhp\Cacheer;

$options = [
    "cacheDir" =>  __DIR__ . "/cache",
];

$Cacheer = new Cacheer($options);

$namespace = 'session_data_01';
$cacheKey = 'session_456';
$sessionData = [
    'user_id' => 456,
    'login_time' => time(),
];

Cacheer::putCache($cacheKey, $sessionData, $namespace);
$Cacheer->putCache($cacheKey, $sessionData, $namespace);

$cachedSessionData = $Cacheer->getCache($cacheKey, $namespace);
```

## Exemplo 05 — Cache de Resposta de API

```php
require_once __DIR__ . "/../vendor/autoload.php";

use Silviooosilva\CacheerPhp\Cacheer;

$options = [
    "cacheDir" =>  __DIR__ . "/cache",
];

$Cacheer = new Cacheer($options);

$apiUrl = 'https://jsonplaceholder.typicode.com/posts';
$cacheKey = 'api_response_' . md5($apiUrl);

$cachedResponse = $Cacheer->getCache($cacheKey);

if ($Cacheer->has($cacheKey)) {
    $response = $cachedResponse;
} else {
    $response = file_get_contents($apiUrl);
    $Cacheer->putCache($cacheKey, $response);
}

$data = json_decode($response, true);
```

## Exemplo 06 — Saída JSON

```php
require_once  __DIR__  .  "/../vendor/autoload.php";
use Silviooosilva\CacheerPhp\Cacheer;

$options = [
  "cacheDir" => __DIR__  .  "/cache",
];

$Cacheer = new Cacheer($options, $formatted = true);

$cacheKey = 'user_profile_1234';
$userProfile = ['id' => 123, 'name' => 'John Doe'];

Cacheer::putCache($cacheKey, $userProfile);
$Cacheer->putCache($cacheKey, $userProfile);

$cachedProfile = $Cacheer->getCache($cacheKey, $namespace, $ttl)->toJson();
```

## Exemplo 07 — Saída Array

```php
require_once  __DIR__  .  "/../vendor/autoload.php";
use Silviooosilva\CacheerPhp\Cacheer;

$options = [
  "cacheDir" => __DIR__  .  "/cache",
];

$Cacheer = new Cacheer($options, $formatted = true);

$cacheKey = 'user_profile_1234';
$userProfile = ['id' => 123, 'name' => 'John Doe'];

Cacheer::putCache($cacheKey, $userProfile);
$Cacheer->putCache($cacheKey, $userProfile);

$cachedProfile = $Cacheer->getCache($cacheKey, $namespace, $ttl)->toArray();
```

## Exemplo 08 — Saída String

```php
require_once  __DIR__  .  "/../vendor/autoload.php";
use Silviooosilva\CacheerPhp\Cacheer;

$options = [
  "cacheDir" => __DIR__  .  "/cache",
];

$Cacheer = new Cacheer($options, $formatted = true);

$cacheKey = 'user_profile_1234';
$userProfile = ['id' => 123, 'name' => 'John Doe'];

Cacheer::putCache($cacheKey, $userProfile);
$Cacheer->putCache($cacheKey, $userProfile);

$cachedProfile = $Cacheer->getCache($cacheKey, $namespace, $ttl)->toString();
```

## Exemplo 09 — Limpeza Automática com `flushAfter`

```php
require_once  __DIR__  .  "/../vendor/autoload.php";
use Silviooosilva\CacheerPhp\Cacheer;

$options = [
  "cacheDir" => __DIR__  .  "/cache",
  "flushAfter" => "1 week"
];

$Cacheer = new Cacheer($options);

$cacheKey = 'user_profile_1234';
$userProfile = ['id' => 123, 'name' => 'John Doe'];

Cacheer::putCache($cacheKey, $userProfile);
$Cacheer->putCache($cacheKey, $userProfile);

$cachedProfile = $Cacheer->getCache($cacheKey);
```

Intervalos aceitos: seconds, minutes, hours, days, weeks, months, years.


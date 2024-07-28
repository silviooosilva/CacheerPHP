## Exemplo 09

<p>Limpeza automática do diretório de cache (flushAfter)</p>

Para usar a limpeza automática do diretório de cache, precisará configurar as options:

```php

<?php

require_once  __DIR__  .  "/../vendor/autoload.php";
use Silviooosilva\CacheerPhp\Cacheer;

$options = [
"cacheDir" => __DIR__  .  "/cache",
"flushAfter" => "1 week" //string
];

$Cacheer = new  Cacheer($options);

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
echo  "Cache Found: ";
print_r($cachedProfile);
} else {
echo  $Cacheer->getMessage();
}


```

Os formatos de tempo aceitos para `flushAfter` são:

```php

Segundos: second(s)
Minutos: minute(s)
Horas: hour(s)
Dias: day(s)
Semanas: week(s)
Meses: month(s)
Anos: year(s)

```

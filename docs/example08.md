
## Exemplo 08

<p>Saída de Dados do Tipo String</p>
Para configurar o tipo de saída que quiser, terá que habilita - lá no momento que instanciar a classe, passando como true o último parametro:

```php

<?php

require_once  __DIR__  .  "/../vendor/autoload.php";
use Silviooosilva\CacheerPhp\Cacheer;

$options = [
"cacheDir" => __DIR__  .  "/cache",
];

$Cacheer = new  Cacheer($options, $formatted = true); // True o último parametro

// Dados a serem armazenados no cache

$cacheKey = 'user_profile_1234';

$userProfile = [
'id' => 123,
'name' => 'John Doe',
'email' => 'john.doe@example.com',
];

// Armazenando dados no cache

$Cacheer->putCache($cacheKey, $userProfile);

// Recuperando dados do cache no formato JSON

$cachedProfile = $Cacheer->getCache(
$cacheKey, 
$namespace, 
$ttl)->toString();

if ($Cacheer->isSuccess()) {
echo  "Cache Found: ";
print_r($cachedProfile);
} else {
echo  $Cacheer->getMessage();
}

```
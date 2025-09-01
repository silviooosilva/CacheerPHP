# Primeiros Passos

CacheerPHP é uma biblioteca PHP minimalista para cache com múltiplos drivers (arquivos, bancos de dados, Redis, array), TTL flexível, compressão e criptografia opcionais e um OptionBuilder fluente.

## Requisitos

- PHP 8.0+
- Opcional: drivers PDO para MySQL, PostgreSQL ou SQLite
- Opcional: servidor Redis e `predis/predis` ao usar o Redis

## Instalação

Instale via Composer:

```sh
composer require silviooosilva/cacheer-php
```

## Configuração

Copie o arquivo de exemplo e ajuste as variáveis conforme necessário:

```sh
cp .env.example .env
```

Veja todas as variáveis suportadas em `docs/configuration.md`.

## Início Rápido

```php
require_once __DIR__ . '/vendor/autoload.php';

use Silviooosilva\CacheerPhp\Cacheer;

$key   = 'user_profile_1234';
$value = ['id' => 123, 'name' => 'John Doe'];

// Configuração estática
Cacheer::setConfig()->setTimeZone('UTC');
Cacheer::setDriver()->useArrayDriver();

// Uso estático com retorno booleano
Cacheer::putCache($key, $value);
if (Cacheer::has($key)) {
    $cached = Cacheer::getCache($key);
    var_dump($cached);
}

// Uso dinâmico com isSuccess()
$cache = new Cacheer([
    'cacheDir' => __DIR__ . '/cache',
]);
$cache->has($key);
if ($cache->isSuccess()) {
    $cached = $cache->getCache($key);
    var_dump($cached);
} else {
    echo $cache->getMessage();
}
```

## Próximos Passos

- API: `docs/pt/api/referencia.md`
- Receitas (Exemplos): `docs/pt/recipes/exemplos.md`


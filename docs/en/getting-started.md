# Getting Started

CacheerPHP is a minimalist PHP caching library with multiple storage drivers (files, databases, Redis, array), flexible TTL handling, optional compression and encryption, and a fluent OptionBuilder.

## Requirements

- PHP 8.0+
- Optional: PDO drivers for MySQL, PostgreSQL or SQLite
- Optional: Redis server and `predis/predis` when using Redis

## Installation

Install via Composer:

```sh
composer require silviooosilva/cacheer-php
```

## Configuration

Copy the example environment file and adjust variables as needed:

```sh
cp .env.example .env
```

See all supported environment variables in `docs/configuration.md`.

## Quick Start

```php
require_once __DIR__ . '/vendor/autoload.php';

use Silviooosilva\CacheerPhp\Cacheer;

$key   = 'user_profile_1234';
$value = ['id' => 123, 'name' => 'John Doe'];

// Configure cache and driver statically
Cacheer::setConfig()->setTimeZone('UTC');
Cacheer::setDriver()->useArrayDriver();

// Static usage with boolean return
Cacheer::putCache($key, $value);
if (Cacheer::has($key)) {
    $cached = Cacheer::getCache($key);
    var_dump($cached);
}

// Dynamic usage and isSuccess()
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

## Next Steps

- [API](./api/reference.md)
- [Examples](./recipes/examples.md)


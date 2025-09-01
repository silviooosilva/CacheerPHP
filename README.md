# CacheerPHP

<p align="center">
  <a href="https://github.com/silviooosilva/CacheerPHP"><img src="docs/cacheer_php_logo__.png" width="450"/></a>
</p>

[![Maintainer](https://img.shields.io/badge/maintainer-@silviooosilva-blue.svg?style=for-the-badge&color=blue)](https://github.com/silviooosilva)
![Packagist Dependency Version](https://img.shields.io/packagist/dependency-v/silviooosilva/cacheer-php/PHP?style=for-the-badge&color=blue)
[![Latest Version](https://img.shields.io/github/release/silviooosilva/CacheerPHP.svg?style=for-the-badge&color=blue)](https://github.com/silviooosilva/CacheerPHP/releases)
[![Quality Score](https://img.shields.io/scrutinizer/g/silviooosilva/CacheerPHP.svg?style=for-the-badge&color=blue)](https://scrutinizer-ci.com/g/silviooosilva/CacheerPHP)
![Packagist Downloads](https://img.shields.io/packagist/dt/silviooosilva/cacheer-php?style=for-the-badge&color=blue)

CacheerPHP is a minimalist PHP caching library. Version 4 brings a more robust API, optional compression and encryption and support for multiple backends including files, databases and Redis.

---

## Features

- **Multiple storage drivers:** file system, databases (MySQL, PostgreSQL, SQLite), Redis and in-memory arrays.
- **Customizable expiration:** define precise TTL (Time To Live) values.
- **Automatic and manual flushing:** clean cache directories with `flushAfter` or on demand.
- **Namespaces:** group entries by category for easier management.
- **Flexible output formatting:** return cached data as JSON, arrays, strings or objects.
- **Compression & encryption:** reduce storage footprint and secure cache contents.
- **OptionBuilder:** fluent builders to configure File, Redis and Database drivers without typos (supports default TTL via `expirationTime` and auto-flush via `flushAfter`).
- **Advanced logging and statistics:** monitor hits/misses and average times (*coming soon*).

## Requirements

- PHP 8.0+
- Optional extensions: PDO drivers for MySQL, PostgreSQL or SQLite
- Redis server and `predis/predis` if using the Redis driver

## Installation

Install via [Composer](https://getcomposer.org):

```sh
composer require silviooosilva/cacheer-php
```

## Configuration

Copy the example environment file and adjust the settings for your environment:

```sh
cp .env.example .env
```

Environment variables control database and Redis connections. See [Configuration](docs/configuration.md) for the full list.

## Quick start

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

// Alternatively, check the state via isSuccess()
$cache->has($key);
if ($cache->isSuccess()) {
    $cached = $cache->getCache($key);
    var_dump($cached);
}
```

## Documentation

- English
  - [Getting Started]: (docs/en/getting-started.md)
  - [API Reference]: (docs/en/api/reference.md)
  - [Examples]: (docs/en/recipes/examples.md)

- Português (PT-BR)
  - [Primeiros Passos]: (docs/pt/getting-started.md)
  - [Referência da API]: (docs/pt/api/referencia.md)
  - [Exemplos]: (docs/pt/recipes/exemplos.md)

Runnable PHP samples live in the `Examples/` directory.

## Testing

After installing dependencies, run the test suite with:

```sh
vendor/bin/phpunit
```

## Contributing

Contributions are welcome! Please open an issue or submit a pull request on GitHub.

## License

CacheerPHP is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Support

If this project helps you, consider [buying the maintainer a coffee](https://buymeacoffee.com/silviooosilva).
<p><a href="https://buymeacoffee.com/silviooosilva"> <img align="left" src="https://cdn.buymeacoffee.com/buttons/v2/default-yellow.png" height="50" width="210" alt="silviooosilva" /></a></p><br><br>

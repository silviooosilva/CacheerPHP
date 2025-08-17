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
- **OptionBuilder:** fluent builder that helps configure drivers without typos (currently for file driver).
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

$cache = new Cacheer([
    'cacheDir' => __DIR__ . '/cache',
]);

$key   = 'user_profile_1234';
$value = ['id' => 123, 'name' => 'John Doe'];

// Store data
$cache->putCache($key, $value);

// Retrieve data
$cached = $cache->getCache($key);

if ($cache->isSuccess()) {
    var_dump($cached);
} else {
    echo $cache->getMessage();
}
```

## Documentation

- [Storing and retrieving cached data](docs/example01.md)
- [Customizable cache expiration](docs/example02.md)
- [Cache flushing and cleaning](docs/example03.md)
- [Namespace support for cache organization](docs/example04.md)
- [Automatic cleaning of the `flushAfter` cache directory](docs/example09.md)
- [API Response Cache](docs/example05.md)
- [Custom Data Output (`JSON`)](docs/example06.md)
- [Custom Data Output (`Array`)](docs/example07.md)
- [Custom Data Output (`String`)](docs/example08.md)
- [Upgrade Guide for Version 2.0.0](docs/guide2.0.0.md)
- [API Reference](docs/api-reference.md)
- [API Reference - Cache Functions](docs/API-Reference/FuncoesCache/README.md)

Additional examples are available in the `Examples` directory.

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
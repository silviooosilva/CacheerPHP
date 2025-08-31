## API Reference

The **OptionBuilder** allows you to define different parameters for configuring CacheerPHP, giving it more security, robustness and speed of execution, as well as excluding possible errors, such as typos, for example.

Also check out the **TimeBuilder**: [TimeBuilder - Introduction](./OptionBuilder/TimeBuilder.md)

It supports builders for File, Redis and Database stores, helping you define options fluently and safely.

Here are some examples:

[FileCacheStore - Example01](../example01.md)

[FileCacheStore - Example02](../example02.md)

You've seen that parameters are very susceptible to typing errors, right?
The **OptionBuilder** arises from the need to eliminate these possible errors.

#### `setUp(array $options): void`

Sets up the `Cacheer` instance with the provided options.

- **Parameters**:
  - `array $options`: An associative array of configuration options (e.g., `driver`, `path`, etc.).

- **Example**:
  ```php
  $cache = new Cacheer();
  $options = [
      'driver' => 'file',
      'path' => '/tmp/cache',
  ];
  $cache->setUp($options);
  ```

#### `getOptions(): array`

Retrieves the current configuration options for the `Cacheer` instance.

- **Returns**:
  - `array`: The current configuration options.

- **Example**:
  ```php
  $cache = new Cacheer();
  $options = $cache->getOptions();
  var_dump($options);
  ```

#### `OptionBuilder()`

The **OptionBuilder** has specific methods for configuring each type of cache driver supported.
Each one initializes the configuration for a given driver and returns an instance of the corresponding builder.

`forFile()`

```php
<?php
$Options = OptionBuilder::forFile();
```
This method initializes FileCacheStore, allowing you to configure the cache directory, expiration time and periodic clearing of the cache.

Methods available after `forFile()`

```sh
dir(string $path) → Defines the directory where the cache files will be stored.
expirationTime(string $time) → Sets the expiration time of the files in the cache.
flushAfter(string $interval) → Sets a time to automatically flush the files from the cache.
build() → Finalizes the configuration and returns an array of options ready for use.
```

**Example of use**

```php
<?php
require_once __DIR__ . "/../vendor/autoload.php"; 

$Options = OptionBuilder::forFile()
    ->dir(__DIR__ . "/cache")
    ->expirationTime("2 hours")
    ->flushAfter("1 day")
    ->build();

$Cacheer = new Cacheer($Options);
$Cacheer->setDriver()->useFileDriver(); //File Driver
```

> **Note:** Cacheer methods may also be called statically, e.g. `Cacheer::setDriver()->useFileDriver();`

`forRedis()`

```php
<?php
$Options = OptionBuilder::forRedis()
    ->setNamespace('app:')
    ->expirationTime('2 hours')
    ->flushAfter('1 day')
    ->build();
```
This method initializes Redis options. You can set a key namespace prefix and optionally default expiration/auto-flush intervals.

Behavior:
- `expirationTime` sets a default TTL used when you do not pass a TTL to `putCache()` or when you pass the implicit default of `3600`. Explicit TTL values other than `3600` always take precedence.
- `flushAfter` enables an auto-flush check on store initialization. If the last flush timestamp is older than the interval, Cacheer will execute `flushCache()` for the Redis store namespace.

Methods available after `forRedis()`

```sh
setNamespace(string $prefix) → Sets the key namespace prefix.
expirationTime(string $time) → Sets a default TTL hint.
flushAfter(string $interval) → Sets an auto-flush hint.
build() → Finalizes and returns an options array.
```

`forDatabase()`

```php
<?php
$Options = OptionBuilder::forDatabase()
    ->table('cache_items')
    ->expirationTime('1 day')
    ->flushAfter('7 days')
    ->build();
```
This method initializes Database options. You can set the storage table and optional time controls.

Behavior:
- `expirationTime` sets a default TTL used when you do not pass a TTL to `putCache()` or when you pass the implicit default of `3600`. Explicit TTL values other than `3600` always take precedence.
- `flushAfter` enables an auto-flush check on store initialization. If the last flush timestamp is older than the interval, Cacheer will execute `flushCache()` for the configured table.

Methods available after `forDatabase()`

```sh
table(string $table) → Defines the table used for storage.
expirationTime(string $time) → Sets a default TTL hint.
flushAfter(string $interval) → Sets an auto-flush hint.
build() → Finalizes and returns an options array.
```

The **OptionBuilder** simplifies the configuration of the **CacheerPHP** by eliminating typing errors and making the process more intuitive.
Now all you have to do is choose the method corresponding to the desired driver and set the necessary parameters to ensure efficient and optimized caching. 🚀

Examples
---

Redis with default TTL and auto-flush:

```php
$options = OptionBuilder::forRedis()
  ->setNamespace('app:')
  ->expirationTime('2 hours')
  ->flushAfter('1 day')
  ->build();

$cache = new Cacheer($options);
$cache->setDriver()->useRedisDriver();

// Uses default TTL (2 hours)
$cache->putCache('session_123', ['id' => 123]);

// Explicit TTL overrides default (10 minutes)
$cache->putCache('session_456', ['id' => 456], '', '10 minutes');
```

Database with custom table, default TTL and auto-flush:

```php
$options = OptionBuilder::forDatabase()
  ->table('cache_items')
  ->expirationTime('30 minutes')
  ->flushAfter('7 days')
  ->build();

$cache = new Cacheer($options);
$cache->setDriver()->useDatabaseDriver();

// Uses default TTL (30 minutes)
$cache->putCache('user_1', ['name' => 'Jane']);
```

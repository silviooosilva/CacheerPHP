## API Reference

Always define the driver to be used in the first instance, and only then define the settings using **setConfig()**.

Check it out below:
[API Reference - setDriver()](setDriver.md)

#### `setConfig()`

```php
<?php

require_once __DIR__ . "/../vendor/autoload.php"; 

$Cacheer = new Cacheer();
$Cacheer->setConfig();
```

Configures the database for storing the cache.
```php
<?php

require_once __DIR__ . "/../vendor/autoload.php"; 

$Cacheer = new Cacheer();
$Cacheer->setConfig()->setDatabaseConnection(string $driver)
```

- Parameters:

```php
$driver: Database driver. Possible values: 'mysql', 'pgsql', 'sqlite'.
```

**Example:**

```php
<?php

require_once __DIR__ . "/../vendor/autoload.php"; 

$Cacheer = new Cacheer();
$Cacheer->setConfig()->setDatabaseConnection('mysql');
```

There is also an alternative, which is to define the driver in the .env file, through the DB_CONNECTION variable, passing the same values.

Timezone
---

```php
<?php

require_once __DIR__ . "/../vendor/autoload.php"; 

$Cacheer = new Cacheer();
$Cacheer->setConfig()->setTimeZone(string $timezone);
```

Sets the time zone for cache operations.
- Parameters

```php
$timezone: Time zone in PHP format (example: 'UTC', 'Africa/Luanda').
```

**Example:**

```php
$Cacheer->setConfig()->setTimeZone('UTC');
```

Check out the timezones supported by PHP here: 
https://www.php.net/manual/en/timezones.php

Logger
---

```php
$Cacheer->setConfig()->setLoggerPath(string $path);
```
Defines the path where the logs will be stored.

- Parameters

```php
$path: Caminho completo para o arquivo de logs.
```

**Example:**

```php
$Cacheer->setConfig()->setLoggerPath('/path/to/logs/CacheerPHP.log');
```
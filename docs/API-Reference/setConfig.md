## API Reference

Defina sempre em primeira instância o driver a ser usado, e só depois defina as configurações, através do **setConfig()**.

Confira abaixo:
[API Reference - setDriver()](setDriver.md)

#### `setConfig()`

```php
<?php

require_once __DIR__ . "/../vendor/autoload.php"; 

$Cacheer = new Cacheer();
$Cacheer->setConfig();
```

Configura o banco de dados para armazenamento do cache.
```php
<?php

require_once __DIR__ . "/../vendor/autoload.php"; 

$Cacheer = new Cacheer();
$Cacheer->setConfig()->setDatabaseConnection(string $driver)
```

- Parâmetros:
```
$driver: Driver do banco de dados. Valores possíveis: 'mysql', 'pgsql', 'sqlite'.
```

**Exemplo:**

```php
<?php

require_once __DIR__ . "/../vendor/autoload.php"; 

$Cacheer = new Cacheer();
$Cacheer->setConfig()->setDatabaseConnection('mysql');
```

Há ainda uma alternativa, que é definir o driver no arquivo .env, através da variável DB_CONNECTION, passando os mesmos valores.

Timezone
---

```php
<?php

require_once __DIR__ . "/../vendor/autoload.php"; 

$Cacheer = new Cacheer();
$Cacheer->setConfig()->setTimeZone(string $timezone);
```

Define o fuso horário para operações de cache.
- Parâmetros

```
$timezone: Fuso horário no formato PHP (exemplo: 'UTC', 'Africa/Luanda').
```

**Exemplo:**

```
$Cacheer->setConfig()->setTimeZone('UTC');
```

Confira aqui, timezones suportados pelo PHP: 
https://www.php.net/manual/en/timezones.php 

Logger
---

```
$Cacheer->setConfig()->setLoggerPath(string $path);
```
Define o caminho onde os logs serão armazenados.

- Parâmetros

```
$path: Caminho completo para o arquivo de logs.
```

**Exemplo:**

```
$Cacheer->setConfig()->setLoggerPath('/path/to/logs/CacheerPHP.log');
```
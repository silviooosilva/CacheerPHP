# Release of Version 2.0.0 of CacheerPHP

We are excited to announce the release of **version 2.0.0** of **CacheerPHP**! This release brings a number of new features and improvements that increase flexibility and choice for developers looking to manage caching efficiently.

## Main New Features of Version 2.0.0

- **Database Support**: CacheerPHP now supports cache storage in **databases** with options for `MySQL`, `SQLite`, and `PostgreSQL`. This allows for greater flexibility, scalability, and performance in various usage scenarios.
- **Performance Improvements**: Additional optimizations for cache retrieval and insertion, ensuring greater efficiency, especially in systems with high data volume.
- **New Features**: It is now possible to monitor the operation of the cache system with the new logging feature. Errors, warnings, information, and debug messages are recorded and stored, providing a clear view of system performance and making it easier to identify and solve potential issues.

## Benefits of the Update

With **version 2.0.0**, you get:

- **Flexibility** to choose the best cache storage solution for your application.
- **Better performance**, with improvements in the process of retrieving and storing cached data.

---

# Upgrade Guide for CacheerPHP 2.0.0

To ensure a smooth transition to version 2.0.0, follow this detailed upgrade manual.

## System Requirements

- **PHP** version 8.0 or higher.
- **Database (optional)**: MySQL, PostgreSQL, or SQLite (for using the database-based cache driver).

## Step-by-Step Upgrade

### 1. Backup Current Cache Data

Before starting the upgrade, it is recommended to back up any relevant cache data. If you are using file-based cache, save the cache directory.

### 2. Update the Package via Composer

Run the command below to update to the latest version of CacheerPHP:

```bash
composer require silviooosilva/cacheer-php:^2.0.0
```

### 3. Configuration

After the update, follow the instructions below to configure the new version.

**Keep File-Based Cache**

If you already use file-based cache and want to keep this configuration, no further action is needed.

**Migrate to Database-Based Cache**

#### 1) Configure Connection Data

- Edit the CacheerPHP configuration file, located in the ```Boot/config.php``` folder, and enter your database details.

#### 2) Enable the Database Driver

- Example usage in code:

```php
<?php
require_once __DIR__ . "/../vendor/autoload.php";

use Silviooosilva\CacheerPhp\Cacheer;

$Cacheer = new Cacheer();
$Cacheer->setConfig()->setDatabaseConnection('mysql');
$Cacheer->setDriver()->useDatabaseDriver();

```

> These configuration steps can also be performed statically using `Cacheer::setConfig()->setDatabaseConnection('mysql');`

#### 3) Configure Timezone

- To avoid issues with cache expiration, set the timezone:

```php
$Cacheer->setConfig()->setTimeZone('Africa/Luanda');
```
**NB: Make sure the provided timezone is valid**
- https://www.php.net/manual/en/timezones.php 

#### 4) Logging System

- Set the path to save the logs:

```php
$Cacheer->setConfig()->setLoggerPath('/path/CacheerPHP.log');
```
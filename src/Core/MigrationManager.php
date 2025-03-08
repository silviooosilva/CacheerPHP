<?php

namespace Silviooosilva\CacheerPhp\Core;

use PDO;
use PDOException;

/**
 * Class MigrationManager
 * @author Sílvio Silva <https://github.com/silviooosilva>
 * @package Silviooosilva\CacheerPhp
 */
class MigrationManager
{
    /**
    * @return void
    */
    public static function migrate(PDO $connection)
    {
        $driver = $connection->getAttribute(PDO::ATTR_DRIVER_NAME);
        $createdAtDefault = ($driver === 'pgsql') ? 'DEFAULT NOW()' : 'DEFAULT CURRENT_TIMESTAMP';

        try {
            if ($driver !== 'sqlite') {
                $dbname = CACHEER_DATABASE_CONFIG[Connect::getConnection()]['dbname'];
                $connection->exec("USE $dbname");
            }

            $query = ($driver === 'sqlite')
                ? "CREATE TABLE IF NOT EXISTS cacheer_table (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    cacheKey VARCHAR(255) NOT NULL,
                    cacheData TEXT NOT NULL,
                    cacheNamespace VARCHAR(255),
                    expirationTime DATETIME NOT NULL,
                    created_at DATETIME $createdAtDefault
                )"
                : "CREATE TABLE IF NOT EXISTS cacheer_table (
                    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    cacheKey VARCHAR(255) NOT NULL,
                    cacheData LONGTEXT NOT NULL,
                    cacheNamespace VARCHAR(255) NULL,
                    expirationTime DATETIME NOT NULL,
                    created_at TIMESTAMP $createdAtDefault
                )";

            $connection->exec($query);
        } catch (PDOException $exception) {
            throw new PDOException($exception->getMessage(), $exception->getCode());
        }
    }
}

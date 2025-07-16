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
     * Executes the migration process for the database.
     * 
     * @param PDO $connection
     * @return void
     */
    public static function migrate(PDO $connection)
    {
        try {
            self::prepareDatabase($connection);
            $queries = self::getMigrationQueries($connection);
            foreach ($queries as $query) {
                if (trim($query)) {
                    $connection->exec($query);
                }
            }
        } catch (PDOException $exception) {
            throw new PDOException($exception->getMessage(), $exception->getCode());
        }
    }

    /**
     * Prepares the database connection for migration.
     * 
     * @param PDO $connection
     * @return void
     */
    private static function prepareDatabase(PDO $connection): void
    {
        $driver = $connection->getAttribute(PDO::ATTR_DRIVER_NAME);
        if ($driver !== 'sqlite') {
            $dbname = CACHEER_DATABASE_CONFIG[Connect::getConnection()]['dbname'];
            $connection->exec("USE $dbname");
        }
    }

    /**
     * Generates the SQL queries needed for the migration based on the database driver.
     * 
     * @param PDO $connection
     * @return array
     */
    private static function getMigrationQueries(PDO $connection): array
    {
        $driver = $connection->getAttribute(PDO::ATTR_DRIVER_NAME);
        $createdAtDefault = ($driver === 'pgsql') ? 'DEFAULT NOW()' : 'DEFAULT CURRENT_TIMESTAMP';

        if ($driver === 'sqlite') {
            $query = "
                CREATE TABLE IF NOT EXISTS cacheer_table (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    cacheKey VARCHAR(255) NOT NULL,
                    cacheData TEXT NOT NULL,
                    cacheNamespace VARCHAR(255),
                    expirationTime DATETIME NOT NULL,
                    created_at DATETIME $createdAtDefault,
                    UNIQUE(cacheKey, cacheNamespace)
                );
                CREATE INDEX IF NOT EXISTS idx_cacheer_cacheKey ON cacheer_table (cacheKey);
                CREATE INDEX IF NOT EXISTS idx_cacheer_cacheNamespace ON cacheer_table (cacheNamespace);
                CREATE INDEX IF NOT EXISTS idx_cacheer_expirationTime ON cacheer_table (expirationTime);
                CREATE INDEX IF NOT EXISTS idx_cacheer_key_namespace ON cacheer_table (cacheKey, cacheNamespace);
            ";
        } elseif ($driver === 'pgsql') {
            $query = "
                CREATE TABLE IF NOT EXISTS cacheer_table (
                    id SERIAL PRIMARY KEY,
                    cacheKey VARCHAR(255) NOT NULL,
                    cacheData TEXT NOT NULL,
                    cacheNamespace VARCHAR(255),
                    expirationTime TIMESTAMP NOT NULL,
                    created_at TIMESTAMP $createdAtDefault,
                    UNIQUE(cacheKey, cacheNamespace)
                );
                CREATE INDEX IF NOT EXISTS idx_cacheer_cacheKey ON cacheer_table (cacheKey);
                CREATE INDEX IF NOT EXISTS idx_cacheer_cacheNamespace ON cacheer_table (cacheNamespace);
                CREATE INDEX IF NOT EXISTS idx_cacheer_expirationTime ON cacheer_table (expirationTime);
                CREATE INDEX IF NOT EXISTS idx_cacheer_key_namespace ON cacheer_table (cacheKey, cacheNamespace);
            ";
        } else {
            $query = "
                CREATE TABLE IF NOT EXISTS cacheer_table (
                    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    cacheKey VARCHAR(255) NOT NULL,
                    cacheData LONGTEXT NOT NULL,
                    cacheNamespace VARCHAR(255) NULL,
                    expirationTime DATETIME NOT NULL,
                    created_at TIMESTAMP $createdAtDefault,
                    UNIQUE KEY unique_cache_key_namespace (cacheKey, cacheNamespace),
                    KEY idx_cacheer_cacheKey (cacheKey),
                    KEY idx_cacheer_cacheNamespace (cacheNamespace),
                    KEY idx_cacheer_expirationTime (expirationTime),
                    KEY idx_cacheer_key_namespace (cacheKey, cacheNamespace)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ";
        }
        return array_filter(array_map('trim', explode(';', $query)));
    }
}

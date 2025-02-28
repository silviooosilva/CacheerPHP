<?php

namespace Silviooosilva\CacheerPhp\Core;

use Exception;
use PDO;
use PDOException;

/**
 * Class Connect
 * @author Sílvio Silva <https://github.com/silviooosilva>
 * @package Silviooosilva\CacheerPhp
 */
class Connect
{

    /**
     * @var string
     */
    public static string $connection = 'sqlite';

    /**
     * @var array
     */
    private static array $instance;

    /**
     * @var PDOException|null
     */
    private static ?PDOException $error = null;

    /**
     * @param array|null $database
     * @return PDO|null
     */
    public static function getInstance(array $database = null)
    {
        $dbConf = $database ?? CACHEER_DATABASE_CONFIG[self::getConnection()];

        if ($dbConf["driver"] === 'sqlite') {
            $dbName = $dbConf["dbname"];
            $dbDsn = $dbConf["driver"] . ":" . $dbName;
        } else {
            $dbName = "{$dbConf["driver"]}-{$dbConf["dbname"]}@{$dbConf["host"]}";
            $dbDsn = $dbConf["driver"] . ":host=" . $dbConf["host"] . ";dbname=" . $dbConf["dbname"] . ";port=" . $dbConf["port"];
        }

        if (!isset(self::$instance)) {
            self::$instance = [];
        }

        if (empty(self::$instance[$dbName])) {
            try {
                $options = $dbConf["options"] ?? [];
                foreach ($options as $key => $value) {
                    if (is_string($value) && defined($value)) {
                        $options[$key] = constant($value);
                    }
                }
                self::$instance[$dbName] = new PDO(
                    $dbDsn,
                    $dbConf["username"] ?? null,
                    $dbConf["passwd"] ?? null,
                    $options
                );
                self::migrate(self::$instance[$dbName]);
            } catch (PDOException $exception) {
                self::$error = $exception;
                return null;
            }
        }
        return self::$instance[$dbName];
    }

    /**
     * @return void
     */
    public static function migrate(PDO $Connection)
    {
        $driver = $Connection->getAttribute(PDO::ATTR_DRIVER_NAME);
        $createdAtDefault = ($driver === 'pgsql') ? 'DEFAULT NOW()' : 'DEFAULT CURRENT_TIMESTAMP';

        try {
            if ($driver !== 'sqlite') {
                $Connection->exec("USE " . CACHEER_DATABASE_CONFIG[self::getConnection()]['dbname']);
            }
           
            if ($driver === 'sqlite') {
                $query = "CREATE TABLE IF NOT EXISTS cacheer_table (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    cacheKey VARCHAR(255) NOT NULL,
                    cacheData TEXT NOT NULL,
                    cacheNamespace VARCHAR(255),
                    expirationTime DATETIME NOT NULL,
                    created_at DATETIME $createdAtDefault
                )";
            } else {
                $query = "CREATE TABLE IF NOT EXISTS cacheer_table (
                    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    cacheKey VARCHAR(255) NOT NULL,
                    cacheData LONGTEXT NOT NULL,
                    cacheNamespace VARCHAR(255) NULL,
                    expirationTime DATETIME NOT NULL,
                    created_at TIMESTAMP $createdAtDefault
                )";
            }
            $Connection->exec($query);
        } catch (PDOException $exception) {
            self::$error = $exception;
        }
    }

    /**
     * @param string $connection
     * @throws \Exception
     * @return void
     */
    public static function setConnection(string $connection)
    {
        $drivers = ['mysql', 'sqlite', 'pgsql'];
        if (!in_array($connection, $drivers)) {
            throw new Exception("Only ['MySQL(mysql)', 'SQLite(sqlite)', 'PgSQL(pgsql)'] Are available at the moment...");
        }
        self::$connection = $connection;
    }

    /**
     * @return string
     */
    public static function getConnection()
    {
        return self::$connection;
    }

    /**
     * @return PDOException|null
     */
    public static function getError()
    {
        return self::$error;
    }

    /**
     * Connect constructor.
     */
    private function __construct() {}

    /**
     * Connect clone.
     */
    private function __clone() {}
}

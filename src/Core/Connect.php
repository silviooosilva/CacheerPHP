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
    public static string $connection = 'mysql';

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
    public static function getInstance(array $database = null): ?PDO
    {
        $dbConf = $database ?? CACHEER_DATABASE_CONFIG[self::getConnection()];
        $dbName = "{$dbConf["driver"]}-{$dbConf["dbname"]}@{$dbConf["host"]}";
        $dbDsn = $dbConf["driver"] . ":host=" . $dbConf["host"] . ";dbname=" . $dbConf["dbname"] . ";port=" . $dbConf["port"];


        if (!isset(self::$instance)) {
            self::$instance = [];
        }

        if (empty(self::$instance[$dbName])) {
            try {
                self::$instance[$dbName] = new PDO(
                    $dbDsn,
                    $dbConf["username"],
                    $dbConf["passwd"],
                    $dbConf["options"]
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
            $Connection->exec("USE " . CACHEER_DATABASE_CONFIG[self::getConnection()]['dbname']);
            $Connection->exec("CREATE TABLE IF NOT EXISTS cacheer_table (
                id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                cacheKey VARCHAR(255) NOT NULL,
                cacheData LONGTEXT NOT NULL,
                cacheNamespace VARCHAR(255) NULL,
                expirationTime DATETIME NOT NULL,
                created_at TIMESTAMP $createdAtDefault
            )");
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
            throw new Exception("Only ['MySQL', 'SQLite', 'PgSQL'] Are available at the moment...");
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
    public static function getError(): ?PDOException
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

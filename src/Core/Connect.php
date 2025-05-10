<?php

namespace Silviooosilva\CacheerPhp\Core;

use PDO;
use PDOException;
use Silviooosilva\CacheerPhp\Exceptions\ConnectionException;

/**
 * Class Connect
 * @author Sílvio Silva <https://github.com/silviooosilva>
 * @package Silviooosilva\CacheerPhp
 */
class Connect
{
    public static string $connection = 'sqlite';
    private static ?PDOException $error = null;


    /**
    * @param array|null $database
    * @return PDO|null
    */
    public static function getInstance(?array $database = null)
    {
        $pdo = ConnectionFactory::createConnection($database);
        if ($pdo) {
            MigrationManager::migrate($pdo);
        }
        return $pdo;
    }

    /**
    * @param string $connection
    * @return void
    */
    public static function setConnection(string $connection)
    {
        $drivers = ['mysql', 'sqlite', 'pgsql'];
        if (!in_array($connection, $drivers)) {
            throw ConnectionException::create("Only ['MySQL(mysql)', 'SQLite(sqlite)', 'PgSQL(pgsql)'] are available at the moment...");
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

    private function __construct() {}
    private function __clone() {}
}

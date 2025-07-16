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
    /**
    * The default connection type.
    * Currently, it supports 'mysql', 'sqlite', and 'pgsql'.
    *
    * @var string
    */
    public static string $connection = 'sqlite';

    /**
    * Holds the last error encountered during connection attempts.
    *
    * @var PDOException|null
    */
    private static ?PDOException $error = null;


    /**
    * Creates a new PDO instance based on the specified database configuration.
    * 
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
    * Sets the connection type for the database.
    * 
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
    * Gets the current connection type.
    *
    * @return string
    */
    public static function getConnection()
    {
        return self::$connection;
    }

    /**
    * Returns the last error encountered during connection attempts.\
    * 
    * @return PDOException|null
    */
    public static function getError()
    {
        return self::$error;
    }
    
    /**
     * Prevents instantiation of the Connect class.
     * This class is designed to be used statically, so it cannot be instantiated.
     * 
     * @return void
    */    
    private function __construct() {}

    /**
    * Prevents cloning of the Connect instance.
    *
    * @return void
    */
    private function __clone() {}
}

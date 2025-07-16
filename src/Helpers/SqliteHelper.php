<?php

namespace Silviooosilva\CacheerPhp\Helpers;
use Silviooosilva\CacheerPhp\Helpers\EnvHelper;

/**
 * Class SqliteHelper
 * @author Sílvio Silva <https://github.com/silviooosilva>
 * @package Silviooosilva\CacheerPhp
 */
class SqliteHelper
{

    /**
    * Gets the path to the SQLite database file.
    *  
    * @param string $database
    * @param ?string $path
    * @return string
    */
    public static function database(string $database = 'database.sqlite', ?string $path = null)
    {
        return self::getDynamicSqliteDbPath($database, $path);
    }

    /**
    * Gets the path to the SQLite database file dynamically.
    *
    * @param  string $database
    * @param ?string $path
    * @return string
    */
    private static function getDynamicSqliteDbPath(string $database, ?string $path = null)
    {
        $rootPath = EnvHelper::getRootPath();
        $databaseDir = is_null($path) ? $rootPath . '/database' : $rootPath . '/' . $path;
        $dbFile = $databaseDir . '/' . self::checkExtension($database);
        
        if (!is_dir($databaseDir)) {
            self::createDatabaseDir($databaseDir);
        }
        if (!file_exists($dbFile)) {
            self::createDatabaseFile($dbFile);
        }
        
        return $dbFile;
    }

    /**
    * Creates the database directory if it does not exist.
    * 
    * @param string $databaseDir
    * @return void
    */
    private static function createDatabaseDir(string $databaseDir)
    {
        if (!is_dir($databaseDir)) {
            mkdir($databaseDir, 0755, true);
        }
    }

    /**
    * Creates the SQLite database file if it does not exist.
    *
    * @param string $dbFile
    * @return void
    */
    private static function createDatabaseFile(string $dbFile)
    {
        if (!file_exists($dbFile)) {
            file_put_contents($dbFile, '');
        }
    }

    /**
    * Checks if the database name has the correct extension.
    * If not, appends '.sqlite' to the name.
    *
    * @param string $database
    * @return string
    */
    private static function checkExtension(string $database)
    {
        if (strpos($database, '.sqlite') === false) {
            return $database . '.sqlite';
        }
        return $database;
    }

}

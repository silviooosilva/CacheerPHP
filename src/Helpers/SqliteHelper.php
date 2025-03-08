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
     * @param string $database
     * @param string $path
     * @return string
     */
    public static function database(string $database = 'database.sqlite', string $path = null)
    {
        return self::getDynamicSqliteDbPath($database, $path);
    }

    /**
     * @return string
     */
    private static function getDynamicSqliteDbPath(string $database, string $path = null)
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

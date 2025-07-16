<?php

namespace Silviooosilva\CacheerPhp\Helpers;

use Silviooosilva\CacheerPhp\Cacheer;
use Silviooosilva\CacheerPhp\CacheStore\ArrayCacheStore;
use Silviooosilva\CacheerPhp\CacheStore\DatabaseCacheStore;
use Silviooosilva\CacheerPhp\CacheStore\FileCacheStore;
use Silviooosilva\CacheerPhp\CacheStore\RedisCacheStore;
use Silviooosilva\CacheerPhp\Core\Connect;
use Silviooosilva\CacheerPhp\Utils\CacheDriver;

/**
 * Class CacheConfig
 * @author Sílvio Silva <https://github.com/silviooosilva>
 * @package Silviooosilva\CacheerPhp
 */
class CacheConfig
{

    /**
     * @var Cacheer
     */
    protected $cacheer;

    /**
     * CacheConfig constructor.
     *
     * @param Cacheer $cacheer
     */
    public function __construct(Cacheer $cacheer)
    {
        $this->cacheer = $cacheer;
        $this->setTimeZone(date_default_timezone_get());
    }

    /**
     * Sets the default timezone for the application.
     * 
     * @param string $timezone
     * @return $this
     */
    public function setTimeZone($timezone)
    {
        /**
         * Make sure the provided timezone is valid
         * 
         * https://www.php.net/manual/en/timezones.php 
         * */

        if (in_array($timezone, timezone_identifiers_list())) {
            date_default_timezone_set($timezone);
        }
        return $this;
    }

    /**
     * Sets the cache driver for the application.
     * 
     * @return CacheDriver
     */
    public function setDriver()
    {
        return new CacheDriver($this->cacheer);
    }

    /**
     * Sets the logger path for the cache driver.
     * 
     * @param string $path
     * @return mixed
     */
    public function setLoggerPath(string $path)
    {
        
        $cacheDriver = $this->setDriver();
        $cacheDriver->logPath = $path;

        $cacheDriverInstance = $this->cacheer->cacheStore;

        return match (get_class($cacheDriverInstance)) {
            FileCacheStore::class => $cacheDriver->useFileDriver(),
            RedisCacheStore::class => $cacheDriver->useRedisDriver(),
            ArrayCacheStore::class => $cacheDriver->useArrayDriver(),
            DatabaseCacheStore::class => $cacheDriver->useDatabaseDriver(),
            default => $cacheDriver->useDatabaseDriver(),
        };
    }

    /**
     * Sets the database connection type for the application.
     * 
     * @param string $driver
     * @return void
     */
    public function setDatabaseConnection(string $driver)
    {
        Connect::setConnection($driver);
    }
}

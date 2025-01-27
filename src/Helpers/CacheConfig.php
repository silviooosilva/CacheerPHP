<?php

namespace Silviooosilva\CacheerPhp\Helpers;

use Silviooosilva\CacheerPhp\Cacheer;
use Silviooosilva\CacheerPhp\CacheStore\FileCacheStore;
use Silviooosilva\CacheerPhp\Core\Connect;
use Silviooosilva\CacheerPhp\Utils\CacheDriver;

class CacheConfig
{

    /**
     * @var Cacheer 
     */
    protected $cacheer;

    public function __construct(Cacheer $cacheer)
    {
        $this->cacheer = $cacheer;
        $this->setTimeZone(date_default_timezone_get());
    }

    /**
     * @param string $timezone
     * @return $this
     */
    public function setTimeZone($timezone)
    {
        /** 
         * Certifique-se de que o timezone fornecido é válido * 
         * https://www.php.net/manual/en/timezones.php 
         * */

        if (in_array($timezone, timezone_identifiers_list())) {
            date_default_timezone_set($timezone);
        }
        return $this;
    }

    /**
     * @return CacheDriver
     */
    public function setDriver()
    {
        return new CacheDriver($this->cacheer);
    }

    /**
     * @param string $path
     * @return void
     */
    public function setLoggerPath(string $path)
    {
        $cacheDriver = $this->cacheer->cacheStore;
        if ($cacheDriver instanceof FileCacheStore) {
            $this->setDriver()->useFileDriver($path);
            return;
        }
        $this->setDriver()->useDatabaseDriver($path);
    }

    /**
     * @param string $driver
     * @return void
     */
    public function setDatabaseConnection(string $driver)
    {
        Connect::setConnection($driver);
    }
}

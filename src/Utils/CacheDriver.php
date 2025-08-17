<?php

namespace Silviooosilva\CacheerPhp\Utils;

use Exception;
use Silviooosilva\CacheerPhp\Cacheer;
use Silviooosilva\CacheerPhp\CacheStore\ArrayCacheStore;
use Silviooosilva\CacheerPhp\CacheStore\FileCacheStore;
use Silviooosilva\CacheerPhp\CacheStore\RedisCacheStore;
use Silviooosilva\CacheerPhp\CacheStore\DatabaseCacheStore;
use Silviooosilva\CacheerPhp\Exceptions\CacheFileException;
use Silviooosilva\CacheerPhp\Helpers\EnvHelper;

/**
 * Class CacheDriver
 * @author Sílvio Silva <https://github.com/silviooosilva>
 * @package Silviooosilva\CacheerPhp
 */
class CacheDriver
{

    /**
    * @var Cacheer
    */
    protected Cacheer $cacheer;

    /** @param string $logPath */
    public string $logPath = 'cacheer.log';

    /**
     * CacheDriver constructor.
     *
     * @param Cacheer $cacheer
     */
    public function __construct(Cacheer $cacheer)
    {
        $this->cacheer = $cacheer;
    }

    /**
    * Uses the database driver for caching.
    * 
    * @return Cacheer
    */
    public function useDatabaseDriver(): Cacheer
    {
        $this->cacheer->cacheStore = new DatabaseCacheStore($this->logPath);
        return $this->cacheer;
    }

    /**
    * Uses the file driver for caching.
    *
    * @return Cacheer
    */
    public function useFileDriver(): Cacheer
    {
        $this->cacheer->options['loggerPath'] = $this->logPath;
        $this->cacheer->cacheStore = new FileCacheStore($this->cacheer->options);
        return $this->cacheer;
    }

    /**
    * Uses the Redis driver for caching.
    * 
    * @return Cacheer
    */
    public function useRedisDriver(): Cacheer
    {
        $this->cacheer->cacheStore = new RedisCacheStore($this->logPath);
        return $this->cacheer;
    }

    /**
    * Uses the array driver for caching.
    * 
    * @return Cacheer
    */
    public function useArrayDriver(): Cacheer
    {
        $this->cacheer->cacheStore = new ArrayCacheStore($this->logPath);
        return $this->cacheer;
    }

    /**
    * Uses the default driver for caching.
    * 
    * @return Cacheer
    */
    public function useDefaultDriver(): Cacheer
    {
        if (!isset($this->cacheer->options['cacheDir'])) {
            $projectRoot = EnvHelper::getRootPath();
            $cacheDir = $projectRoot . DIRECTORY_SEPARATOR . "CacheerPHP" . DIRECTORY_SEPARATOR . "Cache";
            if ($this->isDir($cacheDir)) {
                $this->cacheer->options['cacheDir'] = $cacheDir;
            } else {
                throw CacheFileException::create("Failed to create cache directory: " . $cacheDir);
            }
        }
        $this->useFileDriver();
        return $this->cacheer;
    }

    /**
    * Checks if the directory exists or creates it.
    *
    * @param mixed $dirName
    * @return bool
    */
    private function isDir(mixed $dirName): bool
    {
      if (is_dir($dirName)) {
          return true;
      }
      return mkdir($dirName, 0755, true);
    }
}

<?php

namespace Silviooosilva\CacheerPhp\Utils;

use Exception;
use Silviooosilva\CacheerPhp\Cacheer;
use Silviooosilva\CacheerPhp\CacheStore\ArrayCacheStore;
use Silviooosilva\CacheerPhp\CacheStore\FileCacheStore;
use Silviooosilva\CacheerPhp\CacheStore\RedisCacheStore;
use Silviooosilva\CacheerPhp\CacheStore\DatabaseCacheStore;
use Silviooosilva\CacheerPhp\Exceptions\CacheFileException;

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
    protected $cacheer;

    /** @param string $logPath */
    public string $logPath = 'cacheer.log';

    public function __construct(Cacheer $cacheer)
    {
        $this->cacheer = $cacheer;
    }

    /**
     * @return Cacheer
     */
    public function useDatabaseDriver()
    {
        $this->cacheer->cacheStore = new DatabaseCacheStore($this->logPath);
        return $this->cacheer;
    }

    /**
     * @throws \Exception
     * @return Cacheer
     */
    public function useFileDriver()
    {
        $this->cacheer->options['loggerPath'] = $this->logPath;
        $this->cacheer->cacheStore = new FileCacheStore($this->cacheer->options);
        return $this->cacheer;
    }

    /**
     * @return Cacheer
     */
    public function useRedisDriver()
    {
        $this->cacheer->cacheStore = new RedisCacheStore($this->logPath);
        return $this->cacheer;
    }

    /**
    * @return Cacheer
    */
    public function useArrayDriver()
    {
        $this->cacheer->cacheStore = new ArrayCacheStore($this->logPath);
        return $this->cacheer;
    }

    /**
     * @return Cacheer
    */
    public function useDefaultDriver()
    {
        if (!isset($this->cacheer->options['cacheDir'])) {
            $projectRoot = dirname(__DIR__, 2);
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
    * @param mixed $dirName
    * @return bool
    */
    private function isDir(mixed $dirName)
    {
      if (is_dir($dirName)) {
          return true;
      }
      return mkdir($dirName, 0755, true);
    }
}

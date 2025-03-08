<?php

namespace Silviooosilva\CacheerPhp\Utils;

use Exception;
use Silviooosilva\CacheerPhp\Cacheer;
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
        if (!isset($this->cacheer->options['cacheDir'])) {
            throw CacheFileException::create("The 'cacheDir' option is required for the file driver.");
        }
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
    public function useDefaultDriver()
    {
        if (!isset($this->cacheer->options['cacheDir'])) {
            $cacheDir = EnvHelper::getRootPath() . "CacheerPHP/Cache";
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

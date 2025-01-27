<?php

namespace Silviooosilva\CacheerPhp\Utils;

use Exception;
use Silviooosilva\CacheerPhp\Cacheer;
use Silviooosilva\CacheerPhp\CacheStore\DatabaseCacheStore;
use Silviooosilva\CacheerPhp\CacheStore\FileCacheStore;


class CacheDriver
{

    /**
     * @var Cacheer 
     */
    protected $cacheer;

    public function __construct(Cacheer $cacheer)
    {
        $this->cacheer = $cacheer;
    }

    public function useDatabaseDriver(string $logPath = 'cacheer.log')
    {
        $this->cacheer->cacheStore = new DatabaseCacheStore($logPath);
        return $this;
    }

    /**
     * @throws \Exception
     * @return Cacheer
     */
    public function useFileDriver(string $logPath = 'cacheer.log')
    {
        if (!isset($this->cacheer->options['cacheDir'])) {
            throw new Exception("The 'cacheDir' option is required for the file driver.");
        }
        $this->cacheer->options['loggerPath'] = $logPath;
        $this->cacheer->cacheStore = new FileCacheStore($this->cacheer->options);
        return $this->cacheer;
    }

    /**
     * @return Cacheer
     */
    public function useDefaultDriver()
    {
        if (!isset($this->cacheer->options['cacheDir'])) {
            $this->cacheer->options['cacheDir'] = __DIR__ . "/../../Examples/cache";
        }
        $this->useFileDriver();
        return $this->cacheer;
    }
}

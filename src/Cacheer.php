<?php

namespace Silviooosilva\CacheerPhp;

use Silviooosilva\CacheerPhp\CacheStore\DatabaseCacheStore;
use Silviooosilva\CacheerPhp\CacheStore\FileCacheStore;
use Silviooosilva\CacheerPhp\Helpers\CacheConfig;
use Silviooosilva\CacheerPhp\Utils\CacheDataFormatter;
use Silviooosilva\CacheerPhp\Utils\CacheDriver;

/**
 * Class CacheerPHP
 * @author Sílvio Silva <https://github.com/silviooosilva>
 * @package Silviooosilva\CacheerPhp
 */
class Cacheer
{
    /**
     * @var string
     */
    private string $message;

    /**
     * @var boolean
     */
    private bool $success;

    /**
     * @var boolean
     */
    private bool $formatted;

    /**
     * @var FileCacheStore | DatabaseCacheStore
     */
    public $cacheStore;

    /**
     * @var array
     */
    public array $options = [];

    public function __construct(array $options = [], $formatted = false)
    {
        $this->formatted = $formatted;
        $this->validateOptions($options);
        $this->setDriver()->useDefaultDriver();
    }

    /**
     * @param string $cacheKey
     * @param string $namespace
     * @param string | int $ttl
     * @return CacheDataFormatter | string
     */
    public function getCache(string $cacheKey, string $namespace = '', string | int $ttl = null)
    {
        $cacheData = $this->cacheStore->getCache($cacheKey, $namespace, $ttl);
        $this->setMessage($this->cacheStore->getMessage(), $this->cacheStore->isSuccess());
        return $this->formatted ? new CacheDataFormatter($cacheData) : $cacheData;
    }

    /**
     * @param string $cacheKey
     * @param mixed $cacheData
     * @param string $namespace
     * @return $this
     */
    public function putCache(string $cacheKey, mixed $cacheData, string $namespace = '', int | string $ttl = 3600)
    {
        $this->cacheStore->putCache($cacheKey, $cacheData, $namespace, $ttl);
        $this->setMessage($this->cacheStore->getMessage(), $this->cacheStore->isSuccess());
        return $this;
    }

    /**
     * @param array $items
     * @param string $namespace
     * @param integer $batchSize
     * @return void
     */
    public function putMany(array $items, string $namespace = '', int $batchSize = 100)
    {
        $this->cacheStore->putMany($items, $namespace, $batchSize);
    }

    /**
     * @param string $cacheKey
     * @param mixed $cacheData
     * @param string $namespace
     * @return void
     */
    public function appendCache(string $cacheKey, mixed $cacheData, string $namespace = '')
    {
        $this->cacheStore->appendCache($cacheKey, $cacheData, $namespace);
        $this->setMessage($this->cacheStore->getMessage(), $this->cacheStore->isSuccess());
    }
    /**
     * @param string $cacheKey
     * @param string $namespace
     * @return void
     */
    public function clearCache(string $cacheKey, string $namespace = '')
    {
        $this->cacheStore->clearCache($cacheKey, $namespace);
        return $this->setMessage($this->cacheStore->getMessage(), $this->cacheStore->isSuccess());
    }

    /**
     * @return void
     */
    public function flushCache()
    {
        $this->cacheStore->flushCache();
        $this->setMessage($this->cacheStore->getMessage(), $this->cacheStore->isSuccess());
    }


    /**
     * @param array $options
     * @return void
     */
    private function validateOptions(array $options)
    {
        $this->options = $options;
    }


    /**
     * @param string $message
     * @param boolean $success
     * @return void
     */
    private function setMessage(string $message, bool $success)
    {
        $this->message = $message;
        $this->success = $success;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return boolean
     */
    public function isSuccess()
    {
        return $this->success;
    }

    /**
     * @return CacheDriver
     */
    public function setDriver()
    {
        return new CacheDriver($this);
    }

    /**
     * @return CacheConfig
     */
    public function setConfig()
    {
        return new CacheConfig($this);
    }

    /**
     * @return void
     */
    public function useFormatter()
    {
        $this->formatted = true;
    }
}

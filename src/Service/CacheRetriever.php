<?php

namespace Silviooosilva\CacheerPhp\Service;

use Closure;
use Silviooosilva\CacheerPhp\Cacheer;
use Silviooosilva\CacheerPhp\Helpers\CacheerHelper;
use Silviooosilva\CacheerPhp\Utils\CacheDataFormatter;

/**
* Class CacheRetriever
* @author Sílvio Silva <https://github.com/silviooosilva>
* @package Silviooosilva\CacheerPhp
*/
class CacheRetriever
{
    /**
    * @var Cacheer
    */
    private Cacheer $cacheer;

    /**
    * CacheRetriever constructor.
    *
    * @param Cacheer $cacheer
    */
    public function __construct(Cacheer $cacheer)
    {
        $this->cacheer = $cacheer;
    }

    /**
    * Retrieves a cache item by its key.
    *
    * @param string $cacheKey
    * @param string $namespace
    * @param int|string $ttl
    * @return mixed
    */
    public function getCache(string $cacheKey, string $namespace = '', int|string $ttl = 3600)
    {
        $cacheData = $this->cacheer->cacheStore->getCache($cacheKey, $namespace, $ttl);
        $this->cacheer->syncState();

        if ($this->cacheer->isSuccess() && ($this->cacheer->isCompressionEnabled() ||   $this->cacheer->getEncryptionKey() !== null)) {
            $cacheData = CacheerHelper::recoverFromStorage($cacheData, $this->cacheer->isCompressionEnabled(), $this->cacheer->getEncryptionKey());
        }

        return $this->cacheer->isFormatted() ? new CacheDataFormatter($cacheData) : $cacheData;
    }

    /**
    * Retrieves multiple cache items by their keys.
    *
    * @param array $cacheKeys
    * @param string $namespace
    * @param int|string $ttl
    * @return array|CacheDataFormatter
    */
    public function getMany(array $cacheKeys, string $namespace = '', int|string $ttl = 3600)
    {
        $cachedData = $this->cacheer->cacheStore->getMany($cacheKeys, $namespace, $ttl);
        $this->cacheer->syncState();

        if ($this->cacheer->isSuccess() && ($this->cacheer->isCompressionEnabled() || $this->cacheer->getEncryptionKey() !== null)) {
            foreach ($cachedData as &$data) {
                $data = CacheerHelper::recoverFromStorage($data, $this->cacheer->isCompressionEnabled(), $this->cacheer->getEncryptionKey());
            }
        }

        return $this->cacheer->isFormatted() ? new CacheDataFormatter($cachedData) : $cachedData;
    }

    /**
    * Retrieves all cache items in a namespace.
    *
    * @param string $namespace
    * @return CacheDataFormatter|mixed
    */
    public function getAll(string $namespace = '')
    {
        $cachedData = $this->cacheer->cacheStore->getAll($namespace);
        $this->cacheer->syncState();

        if ($this->cacheer->isSuccess() && ($this->cacheer->isCompressionEnabled() || $this->cacheer->getEncryptionKey() !== null)) {
            foreach ($cachedData as &$data) {
                $data = CacheerHelper::recoverFromStorage($data, $this->cacheer->isCompressionEnabled(), $this->cacheer->getEncryptionKey());
            }
        }

        return $this->cacheer->isFormatted() ? new CacheDataFormatter($cachedData) : $cachedData;
    }

    /**
    * Retrieves a cache item, deletes it, and returns its data.
    *
    * @param string $cacheKey
    * @param string $namespace
    * @return mixed|null
    */
    public function getAndForget(string $cacheKey, string $namespace = '')
    {
        $cachedData = $this->getCache($cacheKey, $namespace);

        if (!empty($cachedData)) {
            $this->cacheer->setInternalState("Cache retrieved and deleted successfully!", true);
            $this->cacheer->clearCache($cacheKey, $namespace);
            return $cachedData;
        }

        return null;
    }

    /**
    * Retrieves a cache item, or executes a callback to store it if not found.
    *
    * @param string $cacheKey
    * @param int|string $ttl
    * @param Closure $callback
    * @return mixed
    */
    public function remember(string $cacheKey, int|string $ttl, Closure $callback)
    {
        $cachedData = $this->getCache($cacheKey, ttl: $ttl);

        if (!empty($cachedData)) {
            return $cachedData;
        }

        $cacheData = $callback();
        $this->cacheer->putCache($cacheKey, $cacheData, ttl: $ttl);
        return $cacheData;
    }

    /**
    * Retrieves a cache item indefinitely, or executes a callback to store it if not found.
    *
    * @param string $cacheKey
    * @param Closure $callback
    * @return mixed
    */
    public function rememberForever(string $cacheKey, Closure $callback)
    {
        return $this->remember($cacheKey, 31536000 * 1000, $callback);
    }

    /**
    * Checks if a cache item exists.
    *
    * @param string $cacheKey
    * @param string $namespace
    * @return void
    */
    public function has(string $cacheKey, string $namespace = '')
    {
        $this->cacheer->cacheStore->has($cacheKey, $namespace);
        $this->cacheer->syncState();
    }
}

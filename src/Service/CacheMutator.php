<?php

namespace Silviooosilva\CacheerPhp\Service;

use Silviooosilva\CacheerPhp\Cacheer;
use Silviooosilva\CacheerPhp\Helpers\CacheerHelper;

/**
* Class CacheMutator
* @author Sílvio Silva <https://github.com/silviooosilva>
* @package Silviooosilva\CacheerPhp
*/
class CacheMutator
{
    /**
    * @var Cacheer
    */
    private Cacheer $cacheer;

    /**
    * CacheMutator constructor.
    *
    * @param Cacheer $cacheer
    */
    public function __construct(Cacheer $cacheer)
    {
        $this->cacheer = $cacheer;
    }

    /**
    * Adds a cache item if it does not already exist.
    *
    * @param string $cacheKey
    * @param mixed $cacheData
    * @param string $namespace
    * @param int|string $ttl
    * @return bool
    */
    public function add(string $cacheKey, mixed $cacheData, string $namespace = '', int|string $ttl = 3600)
    {
        if (!empty($this->cacheer->getCache($cacheKey, $namespace))) {
            return true;
        }

        $this->putCache($cacheKey, $cacheData, $namespace, $ttl);
        $this->cacheer->setInternalState($this->cacheer->getMessage(), $this->cacheer->isSuccess());

        return false;
    }

    /**
    * Appends data to an existing cache item.
    *
    * @param string $cacheKey
    * @param mixed $cacheData
    * @param string $namespace
    * @return void
    */
    public function appendCache(string $cacheKey, mixed $cacheData, string $namespace = '')
    {
        $this->cacheer->cacheStore->appendCache($cacheKey, $cacheData, $namespace);
        $this->cacheer->syncState();
    }

    /**
    * Clears a specific cache item.
    *
    * @param string $cacheKey
    * @param string $namespace
    * @return void
    */
    public function clearCache(string $cacheKey, string $namespace = '')
    {
        $this->cacheer->cacheStore->clearCache($cacheKey, $namespace);
        $this->cacheer->syncState();
    }

    /**
    * Decrements a numeric cache item by a specified amount.
    *
    * @param string $cacheKey
    * @param int $amount
    * @param string $namespace
    * @return bool
    */
    public function decrement(string $cacheKey, int $amount = 1, string $namespace = '')
    {
        return $this->increment($cacheKey, ($amount * -1), $namespace);
    }

    /**
    * Checks if a cache item exists.
    *
    * @param string $cacheKey
    * @param string $namespace
    * @return void
    */
    public function forever(string $cacheKey, mixed $cacheData)
    {
        $this->putCache($cacheKey, $cacheData, ttl: 31536000 * 1000);
        $this->cacheer->setInternalState($this->cacheer->getMessage(), $this->cacheer->isSuccess());
    }

    /**
    * Flushes the entire cache.
    *
    * @return void
    */
    public function flushCache()
    {
        $this->cacheer->cacheStore->flushCache();
        $this->cacheer->syncState();
    }

    /**
    * Gets a cache item by its key.
    *
    * @param string $cacheKey
    * @param string $namespace
    * @return bool
    */
    public function increment(string $cacheKey, int $amount = 1, string $namespace = '')
    {
        $cacheData = $this->cacheer->getCache($cacheKey, $namespace);

        if (!empty($cacheData) && is_numeric($cacheData)) {
            $this->putCache($cacheKey, (int)($cacheData + $amount), $namespace);
            $this->cacheer->setInternalState($this->cacheer->getMessage(), $this->cacheer->isSuccess());
            return true;
        }

        return false;
    }

    /**
    * Gets a cache item by its key.
    *
    * @param string $cacheKey
    * @param string $namespace
    * @param int|string $ttl
    * @return void
    */
    public function putCache(string $cacheKey, mixed $cacheData, string $namespace = '', int|string $ttl = 3600)
    {
        $data = CacheerHelper::prepareForStorage($cacheData, $this->cacheer->isCompressionEnabled(), $this->cacheer->getEncryptionKey());
        $this->cacheer->cacheStore->putCache($cacheKey, $data, $namespace, $ttl);
        $this->cacheer->syncState();
    }

    /**
    * Puts multiple cache items in a batch.
    *
    * @param array $items
    * @param string $namespace
    * @param int $batchSize
    * @return void
    */
    public function putMany(array $items, string $namespace = '', int $batchSize = 100)
    {
        $this->cacheer->cacheStore->putMany($items, $namespace, $batchSize);
    }

    /**
    * Renews the cache item with a new TTL.
    *
    * @param string $cacheKey
    * @param int|string $ttl
    * @param string $namespace
    * @return void
    */
    public function renewCache(string $cacheKey, int|string $ttl = 3600, string $namespace = '')
    {
        $this->cacheer->cacheStore->renewCache($cacheKey, $ttl, $namespace);
        $this->cacheer->syncState();
    }
}

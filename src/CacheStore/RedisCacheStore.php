<?php

namespace Silviooosilva\CacheerPhp\CacheStore;

use Exception;
use Silviooosilva\CacheerPhp\Utils\CacheLogger;
use Silviooosilva\CacheerPhp\Helpers\CacheRedisHelper;
use Silviooosilva\CacheerPhp\Interface\CacheerInterface;
use Silviooosilva\CacheerPhp\Exceptions\CacheRedisException;
use Silviooosilva\CacheerPhp\CacheStore\CacheManager\RedisCacheManager;

/**
 * Class RedisCacheStore
 * @author Sílvio Silva <https://github.com/silviooosilva>
 * @package Silviooosilva\CacheerPhp
 */
class RedisCacheStore implements CacheerInterface
{
    /** @var */
    private $redis;

    /** @param string $namespace */
    private string $namespace = '';

    /**
     * @var CacheLogger
     */
    private $logger = null;

    /**
     * @var string
     */
    private string $message = '';

    /**
     * @var boolean
     */
    private bool $success = false;


    /**
     * RedisCacheStore constructor.
     *
     * @param string $logPath
     */
    public function __construct(string $logPath)
    {
        $this->redis = RedisCacheManager::connect();
        $this->logger = new CacheLogger($logPath);
    }

    /**
     * Appends data to an existing cache item.
     * 
     * @param string $cacheKey
     * @param mixed  $cacheData
     * @param string $namespace
     * @return void
     */
    public function appendCache(string $cacheKey, mixed $cacheData, string $namespace = '')
    {
        $cacheFullKey = $this->buildKey($cacheKey, $namespace);
        $existingData = $this->getCache($cacheFullKey);

        $mergedCacheData = CacheRedisHelper::arrayIdentifier($existingData, $cacheData);

        $serializedData = CacheRedisHelper::serialize($mergedCacheData);

        if ($this->redis->set($cacheFullKey, $serializedData)) {
            $this->setMessage("Cache appended successfully", true);
        } else {
            $this->setMessage("Something went wrong. Please, try again.", false);
        }

        $this->logger->debug("{$this->getMessage()} from redis driver.");
    }

    /**
     * Builds a unique key for the Redis cache.
     * 
     * @param string $key
     * @param string $namespace
     * @return string
     */
    private function buildKey(string $key, string $namespace)
    {
        return $this->namespace . ($namespace ? $namespace . ':' : '') . $key;
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
        $cacheFullKey = $this->buildKey($cacheKey, $namespace);

        if ($this->redis->del($cacheFullKey) > 0) {
            $this->setMessage("Cache cleared successfully", true);
        } else {
            $this->setMessage("Something went wrong. Please, try again.", false);
        }

        $this->logger->debug("{$this->getMessage()} from redis driver.");
    }

    /**
     * Flushes all cache items in Redis.
     * 
     * @return void
     */
    public function flushCache()
    {
        if ($this->redis->flushall()) {
            $this->setMessage("Cache flushed successfully", true);
        } else {
            $this->setMessage("Something went wrong. Please, try again.", false);
        }

        $this->logger->debug("{$this->getMessage()} from redis driver.");
    }

    /**
     * Retrieves a single cache item by its key.
     * 
     * @param string $cacheKey
     * @param string $namespace
     * @param string|int $ttl
     * @return mixed
     */
    public function getCache(string $cacheKey, string $namespace = '', string|int $ttl = 3600)
    {
        $fullCacheKey = $this->buildKey($cacheKey, $namespace);
        $cacheData = $this->redis->get($fullCacheKey);

        if ($cacheData) {
            $this->setMessage("Cache retrieved successfully", true);
            $this->logger->debug("{$this->getMessage()} from redis driver.");
            return CacheRedisHelper::serialize($cacheData, false);
        }

        $this->setMessage("CacheData not found, does not exists or expired", false);
        $this->logger->info("{$this->getMessage()} from redis driver.");
    }

    /**
     * Retrieves all cache items in a specific namespace.
     * 
     * @param string $namespace
     * @return array
     */
    public function getAll(string $namespace = '')
    {
        $keys = $this->redis->keys($this->buildKey('*', $namespace));
        $results = [];

        foreach ($keys as $key) {
            $cacheData = $this->getCache($key, $namespace);
            if ($cacheData !== null) {
                $results[$key] = $cacheData;
            }
        }

        if (empty($results)) {
            $this->setMessage("No cache data found in the namespace", false);
        } else {
            $this->setMessage("Cache data retrieved successfully", true);
        }

        return $results;
    }

    /**
     * Retrieves multiple cache items by their keys.
     * 
     * @param array $cacheKeys
     * @param string $namespace
     * @param string|int $ttl
     * @return array
     */
    public function getMany(array $cacheKeys, string $namespace = '', string|int $ttl = 3600)
    {
        $results = [];
        foreach ($cacheKeys as $cacheKey) {
            $fullCacheKey = $this->buildKey($cacheKey, $namespace);
            $cacheData = $this->getCache($fullCacheKey, $namespace, $ttl);
            if ($cacheData !== null) {
                $results[$cacheKey] = $cacheData;
            }
        }

        if (empty($results)) {
            $this->setMessage("No cache data found for the provided keys", false);
        } else {
            $this->setMessage("Cache data retrieved successfully", true);
        }

        return $results;
    }

    /**
     * Gets the message from the last operation.
     * 
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Gets the serialized dump of a cache item.
     * 
     * @param string $fullKey
     * @return string|null
     */
    private function getDump(string $fullKey)
    {
        return $this->redis->dump($fullKey);
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
        $cacheFullKey = $this->buildKey($cacheKey, $namespace);

        if ($this->redis->exists($cacheFullKey) > 0) {
            $this->setMessage("Cache Key: {$cacheKey} exists!", true);
        } else {
            $this->setMessage("Cache Key: {$cacheKey} does not exists!", false);
        }

        $this->logger->debug("{$this->getMessage()} from redis driver.");
    }

    /**
     * Checks if the last operation was successful.
     * 
     * @return boolean
     */
    public function isSuccess()
    {
        return $this->success;
    }

    /**
     * Processes a batch of cache items and stores them in Redis.
     * 
     * @param array  $batchItems
     * @param string $namespace
     * @return void
     */
    private function processBatchItems(array $batchItems, string $namespace)
    {
        foreach ($batchItems as $item) {
            CacheRedisHelper::validateCacheItem($item);
            $cacheKey = $item['cacheKey'];
            $cacheData = $item['cacheData'];
            $mergedData = CacheRedisHelper::mergeCacheData($cacheData);
            $this->putCache($cacheKey, $mergedData, $namespace);
        }
    }

    /**
     * Stores a cache item in Redis with optional namespace and TTL.
     *
     * @param string $cacheKey
     * @param mixed  $cacheData
     * @param string $namespace
     * @param string|int|null $ttl
     * @return mixed
     */
    public function putCache(string $cacheKey, mixed $cacheData, string $namespace = '', string|int|null $ttl = null)
    {
        $cacheFullKey = $this->buildKey($cacheKey, $namespace);
        $serializedData = CacheRedisHelper::serialize($cacheData);

        $result = $ttl ? $this->redis->setex($cacheFullKey, (int) $ttl, $serializedData)
                       : $this->redis->set($cacheFullKey, $serializedData);

        if ($result) {
            $this->setMessage("Cache stored successfully", true);
        } else {
            $this->setMessage("Failed to store cache", false);
        }

        $this->logger->debug("{$this->getMessage()} from Redis driver.");
        return $result;
    }

    /**
     * Stores multiple cache items in Redis in batches.
     * 
     * @param array  $items
     * @param string $namespace
     * @param int    $batchSize
     * @return void
     */
    public function putMany(array $items, string $namespace = '', int $batchSize = 100)
    {
        $processedCount = 0;
        $itemCount = count($items);

        while ($processedCount < $itemCount) {
            $batchItems = array_slice($items, $processedCount, $batchSize);
            $this->processBatchItems($batchItems, $namespace);
            $processedCount += count($batchItems);
        }
    }

    /**
     * Renews the cache for a specific key with a new TTL.
     * 
     * @param string $cacheKey
     * @param string|int $ttl
     * @param string $namespace
     * @return void
     */
    public function renewCache(string $cacheKey, string|int $ttl, string $namespace = '')
    {
        $cacheFullKey = $this->buildKey($cacheKey, $namespace);
        $dump = $this->getDump($cacheFullKey);

        if (!$dump) {
            $this->setMessage("Cache Key: {$cacheKey} not found.", false);
            $this->logger->warning("{$this->getMessage()} from Redis driver.");
            return;
        }

        $this->clearCache($cacheFullKey);

        if ($this->restoreKey($cacheFullKey, $ttl, $dump)) {
            $this->setMessage("Cache Key: {$cacheKey} renewed successfully.", true);
            $this->logger->debug("{$this->getMessage()} from Redis driver.");
        } else {
            $this->setMessage("Failed to renew cache key: {$cacheKey}.", false);
            $this->logger->error("{$this->getMessage()} from Redis driver.");
        }
    }

    /**
     * Restores a key in Redis with a given TTL and serialized data.
     * 
     * @param string $fullKey
     * @param string|int $ttl
     * @param mixed $dump
     * @return bool
     */
    private function restoreKey(string $fullKey, string|int $ttl, mixed $dump)
    {
        try {
            $this->redis->restore($fullKey, $ttl * 1000, $dump, 'REPLACE');
            return true;
        } catch (Exception $e) {
            throw CacheRedisException::create($e->getMessage());
        }
    }

    /**
     * Sets a message and its success status.
     * 
     * @param string  $message
     * @param boolean $success
     * @return void
     */
    private function setMessage(string $message, bool $success)
    {
        $this->message = $message;
        $this->success = $success;
    }
}
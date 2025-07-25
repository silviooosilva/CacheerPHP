<?php

namespace Silviooosilva\CacheerPhp\CacheStore;

use Silviooosilva\CacheerPhp\Interface\CacheerInterface;
use Silviooosilva\CacheerPhp\Helpers\CacheDatabaseHelper;
use Silviooosilva\CacheerPhp\Utils\CacheLogger;
use Silviooosilva\CacheerPhp\Repositories\CacheDatabaseRepository;

/**
 * Class DatabaseCacheStore
 * @author Sílvio Silva <https://github.com/silviooosilva>
 * @package Silviooosilva\CacheerPhp
 */
class DatabaseCacheStore implements CacheerInterface
{
    /**
     * @param boolean
     */
    private bool $success = false;

    /**
     * @param string
     */
    private string $message = '';

    /**
     * @var CacheLogger
     */
    private $logger = null;

    /**
     * @var CacheDatabaseRepository
     */
    private $cacheRepository;

    /**
     * DatabaseCacheStore constructor.
     *
     * @param string $logPath
     */
    public function __construct(string $logPath)
    {
        $this->logger = new CacheLogger($logPath);
        $this->cacheRepository = new CacheDatabaseRepository();
    }

    /**
     * Appends data to an existing cache item.
     * 
     * @param string $cacheKey
     * @param mixed  $cacheData
     * @param string $namespace
     * @return bool
     */
    public function appendCache(string $cacheKey, mixed $cacheData, string $namespace = '')
    {
        $currentCacheData = $this->getCache($cacheKey, $namespace);
        $mergedCacheData = CacheDatabaseHelper::arrayIdentifier($currentCacheData, $cacheData);

        if ($this->updateCache($cacheKey, $mergedCacheData, $namespace)) {
            $this->logger->debug("{$this->getMessage()} from database driver.");
            return true;
        }

        $this->logger->error("{$this->getMessage()} from database driver.");
        return false;
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
        $data = $this->cacheRepository->clear($cacheKey, $namespace);
        if($data) {
            $this->setMessage("Cache deleted successfully!", true);
        } else {
            $this->setMessage("Cache does not exists!", false);
        }

        $this->logger->debug("{$this->getMessage()} from database driver.");
    }

    /**
     * Flushes all cache items.
     * 
     * @return void
     */
    public function flushCache()
    {
        if($this->cacheRepository->flush()){
            $this->setMessage("Flush finished successfully", true);
        } else {
            $this->setMessage("Something went wrong. Please, try again.", false);
        }

        $this->logger->info("{$this->getMessage()} from database driver.");

    }

    /**
     * Gets a single cache item.
     * 
     * @param string $cacheKey
     * @param string $namespace
     * @param string|int $ttl
     * @return mixed
     */
    public function getCache(string $cacheKey, string $namespace = '', string|int $ttl = 3600)
    {
        $cacheData = $this->retrieveCache($cacheKey, $namespace);
        if ($cacheData) {
            $this->setMessage("Cache retrieved successfully", true);
            $this->logger->debug("{$this->getMessage()} from database driver.");
            return $cacheData;
        }
        $this->setMessage("CacheData not found, does not exists or expired", false);
        $this->logger->info("{$this->getMessage()} from database driver.");
        return null;
    }

    /**
     * Gets all items in a specific namespace.
     * 
     * @param string $namespace
     * @return array
     */
    public function getAll(string $namespace = '')
    {
        $cacheData = $this->cacheRepository->getAll($namespace);
        if ($cacheData) {
            $this->setMessage("Cache retrieved successfully", true);
            $this->logger->debug("{$this->getMessage()} from database driver.");
            return $cacheData;
        }
        $this->setMessage("No cache data found for the provided namespace", false);
        $this->logger->info("{$this->getMessage()} from database driver.");
        return [];
    }

    /**
     * Retrieves multiple cache items by their keys.
     * 
     * @param array  $cacheKeys
     * @param string $namespace
     * @param string|int $ttl
     * @return array
     */
    public function getMany(array $cacheKeys, string $namespace = '', string|int $ttl = 3600)
    {
        $cacheData = [];
        foreach ($cacheKeys as $cacheKey) {
            $data = $this->getCache($cacheKey, $namespace, $ttl);
            if ($data) {
                $cacheData[$cacheKey] = $data;
            }
        }
        if (!empty($cacheData)) {
            $this->setMessage("Cache retrieved successfully", true);
            $this->logger->debug("{$this->getMessage()} from database driver.");
            return $cacheData;
        }
        $this->setMessage("No cache data found for the provided keys", false);
        $this->logger->info("{$this->getMessage()} from database driver.");
        return [];
    }

    /**
     * Checks if a cache item exists.
     * 
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
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
        $cacheData = $this->getCache($cacheKey, $namespace);
        if ($cacheData) {
            $this->logger->debug("Cache key: {$cacheKey} exists and it's available from database driver.");
        }
        $this->logger->warning("{$this->getMessage()} from database driver.");
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
     * Store multiple items in the cache.
     * 
     * @param array   $items
     * @param string  $namespace
     * @param integer $batchSize
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
     * Stores an item in the cache with a specific TTL.
     * 
     * @param string $cacheKey
     * @param mixed  $cacheData
     * @param string $namespace
     * @param string|int $ttl
     * @return bool
     */
    public function putCache(string $cacheKey, mixed $cacheData, string $namespace = '', string|int $ttl = 3600)
    {
        if($this->storeCache($cacheKey, $cacheData, $namespace, $ttl)){
            $this->logger->debug("{$this->getMessage()} from database driver.");
            return true;
        }
        $this->logger->error("{$this->getMessage()} from database driver.");
        return false;
    }

    /**
     * Renews the cache for a specific key with a new TTL.
     * 
     * @param string $cacheKey
     * @param string|int $ttl
     * @param string $namespace
     * @return void
     */
    public function renewCache(string $cacheKey, int | string $ttl, string $namespace = '')
    {
        $cacheData = $this->getCache($cacheKey, $namespace);
        if ($cacheData) {
            $this->renew($cacheKey, $ttl, $namespace);
            $this->setMessage("Cache with key {$cacheKey} renewed successfully", true);
            $this->logger->debug("{$this->getMessage()} from database driver.");
        }
    }

    /**
     * Processes a batch of cache items.
     * 
     * @param array  $batchItems
     * @param string $namespace
     * @return void
     */
    private function processBatchItems(array $batchItems, string $namespace)
    {
        foreach($batchItems as $item) {
            CacheDatabaseHelper::validateCacheItem($item);
            $cacheKey = $item['cacheKey'];
            $cacheData = $item['cacheData'];
            $mergedData = CacheDatabaseHelper::mergeCacheData($cacheData);
            $this->putCache($cacheKey, $mergedData, $namespace);
        }
    }

    /**
     * Renews the expiration time of a cache item.
     * 
     * @param string $cacheKey
     * @param string|int $ttl
     * @param string $namespace
     * @return bool
     */
    private function renew(string $cacheKey, string|int $ttl = 3600, string $namespace = '')
    {
        $cacheData = $this->getCache($cacheKey, $namespace);
        if ($cacheData) {
            $renewedCache = $this->cacheRepository->renew($cacheKey, $ttl, $namespace);
            if ($renewedCache) {
                $this->setMessage("Cache with key {$cacheKey} renewed successfully", true);
                $this->logger->debug("{$this->getMessage()} from database driver.");
                return true;
            }
            return false;
        }
        return false;
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

    /**
     * Retrieves a cache item by its key.
     * @param string $cacheKey
     * @param string $namespace
     * @return mixed
     */
    private function retrieveCache(string $cacheKey, string $namespace = '')
    {
        return $this->cacheRepository->retrieve($cacheKey, $namespace);
    }

    /**
     * Stores a cache item.
     * 
     * @param string $cacheKey
     * @param mixed  $cacheData
     * @param string $namespace
     * @param integer $ttl
     * @return bool
     */
    private function storeCache(string $cacheKey, mixed $cacheData, string $namespace = '', string|int $ttl = 3600)
    {
        $data = $this->cacheRepository->store($cacheKey, $cacheData, $namespace, $ttl);
        if($data) {
            $this->setMessage("Cache Stored Successfully", true);
            return true;
        }
        $this->setMessage("Already exists a cache with this key...", false);
        return false;
    }

    /**
     * Updates an existing cache item.
     * 
     * @param string $cacheKey
     * @param mixed  $cacheData
     * @param string $namespace
     * @return bool
     */
    private function updateCache(string $cacheKey, mixed $cacheData, string $namespace = '')
    {
        $data = $this->cacheRepository->update($cacheKey, $cacheData, $namespace);
        if($data) {
            $this->setMessage("Cache updated successfully.", true);
            return true;
        }
        $this->setMessage("Cache does not exist or update failed!", false);
        return false;
    }
}

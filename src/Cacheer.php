<?php

namespace Silviooosilva\CacheerPhp;

use Closure;
use Silviooosilva\CacheerPhp\Interface\CacheerInterface;
use Silviooosilva\CacheerPhp\CacheStore\DatabaseCacheStore;
use Silviooosilva\CacheerPhp\CacheStore\FileCacheStore;
use Silviooosilva\CacheerPhp\CacheStore\RedisCacheStore;
use Silviooosilva\CacheerPhp\CacheStore\ArrayCacheStore;
use Silviooosilva\CacheerPhp\Helpers\CacheConfig;
use Silviooosilva\CacheerPhp\Utils\CacheDataFormatter;
use Silviooosilva\CacheerPhp\Utils\CacheDriver;
use Silviooosilva\CacheerPhp\Helpers\CacheerHelper;
use RuntimeException;

/**
* Class CacheerPHP
* @author Sílvio Silva <https://github.com/silviooosilva>
* @package Silviooosilva\CacheerPhp
*/
final class Cacheer implements CacheerInterface
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
    private bool $formatted = false;

    /**
    * @var bool
    */
    private bool $compression = false;

    /**
    * @var string|null
    */
    private ?string $encryptionKey = null;

    /**
    * @var FileCacheStore|DatabaseCacheStore|RedisCacheStore|ArrayCacheStore
    */
    public $cacheStore;

    /**
    * @var array
    */
    public array $options = [];

    /**
    * Cacheer constructor.
    *
    * @param array $options
    * @param bool  $formatted
    * @throws RuntimeException
    */
    public function __construct(array $options = [], $formatted = false)
    {
        $this->formatted = $formatted;
        $this->validateOptions($options);
        $this->setDriver()->useDefaultDriver();
    }

    /**
    * Adds data to the cache if it does not already exist.
    *
    * @param string $cacheKey
    * @param mixed  $cacheData
    * @param string $namespace
    * @param int|string $ttl
    * @return bool
    */
    public function add(string $cacheKey, mixed $cacheData, string $namespace = '', int|string $ttl = 3600)
    {
        if (!empty($this->getCache($cacheKey, $namespace))) {
            return true;
        }

        $this->putCache($cacheKey, $cacheData, $namespace, $ttl);
        $this->setMessage($this->getMessage(), $this->isSuccess());

        return false;
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
        $this->cacheStore->appendCache($cacheKey, $cacheData, $namespace);
        $this->setMessage($this->cacheStore->getMessage(), $this->cacheStore->isSuccess());
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
        $this->cacheStore->clearCache($cacheKey, $namespace);
        $this->setMessage($this->cacheStore->getMessage(), $this->cacheStore->isSuccess());
    }

    /**
    * Decrements a cache item by a specified amount.
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
    * Store data in the cache permanently.
    *
    * @param string $cacheKey
    * @param mixed $cacheData
    * @return void
    */
    public function forever(string $cacheKey, mixed $cacheData)
    {
        $this->putCache($cacheKey, $cacheData, ttl: 31536000 * 1000);
        $this->setMessage($this->getMessage(), $this->isSuccess());
    }

    /**
    * Flushes all cache items.
    * 
    * @return void
    */
    public function flushCache()
    {
        $this->cacheStore->flushCache();
        $this->setMessage($this->cacheStore->getMessage(), $this->cacheStore->isSuccess());
    }

    /**
    * Retrieves a cache item and deletes it from the cache.
    * 
    * @param string $cacheKey
    * @param string $namespace
    * @return mixed
    */
    public function getAndForget(string $cacheKey, string $namespace = '')
    {
        $cachedData = $this->getCache($cacheKey, $namespace);

        if (!empty($cachedData)) {
            $this->setMessage("Cache retrieved and deleted successfully!", true);
            $this->clearCache($cacheKey, $namespace);
            return $cachedData;
        }

        return null;
    }

    /**
    * Gets all items in a specific namespace.
    * 
    * @param string $namespace
    * @return CacheDataFormatter|array
    */
    public function getAll(string $namespace = '')
    {
        $cachedData = $this->cacheStore->getAll($namespace);
        $this->setMessage($this->cacheStore->getMessage(), $this->cacheStore->isSuccess());

        if ($this->cacheStore->isSuccess() && ($this->compression || $this->encryptionKey !== null)) {
            foreach ($cachedData as &$data) {
                $data = CacheerHelper::recoverFromStorage($data, $this->compression, $this->encryptionKey);
            }
        }

        return $this->formatted ? new CacheDataFormatter($cachedData) : $cachedData;
    }

    /**
    * Retrieves a single cache item.
    * 
    * @param string $cacheKey
    * @param string $namespace
    * @param string|int $ttl
    * @return CacheDataFormatter|mixed
    */
    public function getCache(string $cacheKey, string $namespace = '', string|int $ttl = 3600)
    {
        $cacheData = $this->cacheStore->getCache($cacheKey, $namespace, $ttl);
        $this->setMessage($this->cacheStore->getMessage(), $this->cacheStore->isSuccess());

        if ($this->cacheStore->isSuccess() && ($this->compression || $this->encryptionKey !== null)) {
            $cacheData = CacheerHelper::recoverFromStorage($cacheData, $this->compression, $this->encryptionKey);
        }

        return $this->formatted ? new CacheDataFormatter($cacheData) : $cacheData;
    }

    /**
    * Retrieves multiple cache items by their keys.
    * 
    * @param array $cacheKeys
    * @param string $namespace
    * @param string|int $ttl
    * @return CacheDataFormatter|array
    */
    public function getMany(array $cacheKeys, string $namespace = '', string|int $ttl = 3600)
    {
        $cachedData = $this->cacheStore->getMany($cacheKeys, $namespace, $ttl);
        $this->setMessage($this->cacheStore->getMessage(), $this->cacheStore->isSuccess());

        if ($this->cacheStore->isSuccess() && ($this->compression || $this->encryptionKey !== null)) {
            foreach ($cachedData as &$data) {
                $data = CacheerHelper::recoverFromStorage($data, $this->compression, $this->encryptionKey);
            }
        }

        return $this->formatted ? new CacheDataFormatter($cachedData) : $cachedData;
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
        $this->cacheStore->has($cacheKey, $namespace);
        $this->setMessage($this->cacheStore->getMessage(), $this->cacheStore->isSuccess());
    }

    /**
    * Increments a cache item by a specified amount.
    * 
    * @param string $cacheKey
    * @param int $amount
    * @param string $namespace
    * @return bool
    */
    public function increment(string $cacheKey, int $amount = 1, string $namespace = '')
    {
        $cacheData = $this->getCache($cacheKey, $namespace);

        if(!empty($cacheData) && is_numeric($cacheData)) {
            $this->putCache($cacheKey, (int)($cacheData + $amount), $namespace);
            $this->setMessage($this->getMessage(), $this->isSuccess());
            return true;
        }

        return false;
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
    * Stores an item in the cache with a specific TTL.
    * 
    * @param string $cacheKey
    * @param mixed  $cacheData
    * @param string $namespace
    * @param string|int $ttl
    * @return void
    */
    public function putCache(string $cacheKey, mixed $cacheData, string $namespace = '', string|int $ttl = 3600)
    {
        $data = CacheerHelper::prepareForStorage($cacheData, $this->compression, $this->encryptionKey);
        $this->cacheStore->putCache($cacheKey, $data, $namespace, $ttl);
        $this->setMessage($this->cacheStore->getMessage(), $this->cacheStore->isSuccess());
    }

    /**
    * Stores multiple items in the cache.
    *  
    * @param array   $items
    * @param string  $namespace
    * @param integer $batchSize
    * @return void
    */
    public function putMany(array $items, string $namespace = '', int $batchSize = 100)
    {
        $this->cacheStore->putMany($items, $namespace, $batchSize);
    }

    /**
    * Renews the cache for a specific key with a new TTL.
    * 
    * @param string $cacheKey
    * @param string|int $ttl
    * @param string $namespace
    * @return void
    */
    public function renewCache(string $cacheKey, string|int $ttl = 3600, string $namespace = '')
    {
        $this->cacheStore->renewCache($cacheKey, $ttl, $namespace);

        if ($this->cacheStore->isSuccess()) {
            $this->setMessage($this->cacheStore->getMessage(), $this->cacheStore->isSuccess());
        } else {
            $this->setMessage($this->cacheStore->getMessage(), $this->cacheStore->isSuccess());
        }
    }

    /**
    * Retrieves a cache item or executes a callback to store it if not found.
    * 
    * @param string $cacheKey
    * @param int|string $ttl
    * @param Closure $callback
    * @return mixed
    */
    public function remember(string $cacheKey, int|string $ttl, Closure $callback)
    {
        $cachedData = $this->getCache($cacheKey, ttl: $ttl);

        if(!empty($cachedData)) {
            return $cachedData;
        }

        $cacheData = $callback();
        $this->putCache($cacheKey, $cacheData, ttl: $ttl);
        $this->setMessage($this->getMessage(), $this->isSuccess());

        return $cacheData;
    }

    /**
    * Retrieves a cache item or executes a callback to store it permanently if not found.
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
    * Returns a CacheConfig instance for configuration management.
    * 
    * @return CacheConfig
    */
    public function setConfig()
    {
        return new CacheConfig($this);
    }

    /**
    * Sets the cache driver based on the configuration.
    * 
    * @return CacheDriver
    */
    public function setDriver()
    {
        return new CacheDriver($this);
    }

    /**
    * Sets a message for the cache operation.
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
    * Retrieves the message from the last operation.
    * 
    * @return string
    */
    public function getMessage()
    {
        return $this->message;
    }

    /**
    * Enables or disables the formatter for cache data.
    * 
    * @return void
    */
    public function useFormatter()
    {
        $this->formatted = !$this->formatted;
    }

    /**
    * Validates the options provided for the Cacheer instance.
    * 
    * @param array $options
    * @return void
    */
    private function validateOptions(array $options)
    {
        $this->options = $options;
    }

    /**
    * Enable or disable data compression
    *
    * @param bool $status
    * @return $this
    */
    public function useCompression(bool $status = true)
    {
        $this->compression = $status;
        return $this;
    }

    /**
    * Enable encryption for cached data
    *
    * @param string $key
    * @return $this
    */
    public function useEncryption(string $key)
    {
        $this->encryptionKey = $key;
        return $this;
    }
}

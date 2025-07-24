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
use Silviooosilva\CacheerPhp\Service\CacheRetriever;
use Silviooosilva\CacheerPhp\Service\CacheMutator;

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

    private CacheRetriever $retriever;
    private CacheMutator $mutator;

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
        $this->retriever = new CacheRetriever($this);
        $this->mutator = new CacheMutator($this);
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
        return $this->mutator->add($cacheKey, $cacheData, $namespace, $ttl);
    }

    /**
    * Appends data to an existing cache item.
    * 
    * @param string $cacheKey
    * @param mixed  $cacheData
    * @param string $namespace
    * @return void
    */
    public function appendCache(string $cacheKey, mixed $cacheData, string $namespace = ''): void
    {
        $this->mutator->appendCache($cacheKey, $cacheData, $namespace);
    }

    /**
    * Clears a specific cache item.
    * 
    * @param string $cacheKey
    * @param string $namespace
    * @return void
    */
    public function clearCache(string $cacheKey, string $namespace = ''): void
    {
        $this->mutator->clearCache($cacheKey, $namespace);
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
        return $this->mutator->decrement($cacheKey, $amount, $namespace);
    }

    /**
    * Store data in the cache permanently.
    *
    * @param string $cacheKey
    * @param mixed $cacheData
    * @return void
    */
    public function forever(string $cacheKey, mixed $cacheData): void
    {
        $this->mutator->forever($cacheKey, $cacheData);
    }

    /**
    * Flushes all cache items.
    * 
    * @return void
    */
    public function flushCache(): void
    {
        $this->mutator->flushCache();
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
        return $this->retriever->getAndForget($cacheKey, $namespace);
    }

    /**
    * Gets all items in a specific namespace.
    * 
    * @param string $namespace
    * @return CacheDataFormatter|mixed
    */
    public function getAll(string $namespace = '')
    {
        return $this->retriever->getAll($namespace);
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
        return $this->retriever->getCache($cacheKey, $namespace, $ttl);
    }

    /**
    * Retrieves multiple cache items by their keys.
    * 
    * @param array $cacheKeys
    * @param string $namespace
    * @param string|int $ttl
    * @return CacheDataFormatter|mixed
    */
    public function getMany(array $cacheKeys, string $namespace = '', string|int $ttl = 3600)
    {
        return $this->retriever->getMany($cacheKeys, $namespace, $ttl);
    }

    /**
    * Checks if a cache item exists.
    * 
    * @param string $cacheKey
    * @param string $namespace
    * @return void
    */
    public function has(string $cacheKey, string $namespace = ''): void
    {
        $this->retriever->has($cacheKey, $namespace);
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
        return $this->mutator->increment($cacheKey, $amount, $namespace);
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
    public function putCache(string $cacheKey, mixed $cacheData, string $namespace = '', string|int $ttl = 3600): void
    {
        $this->mutator->putCache($cacheKey, $cacheData, $namespace, $ttl);
    }

    /**
    * Stores multiple items in the cache.
    *  
    * @param array   $items
    * @param string  $namespace
    * @param integer $batchSize
    * @return void
    */
    public function putMany(array $items, string $namespace = '', int $batchSize = 100): void
    {
        $this->mutator->putMany($items, $namespace, $batchSize);
    }

    /**
    * Renews the cache for a specific key with a new TTL.
    * 
    * @param string $cacheKey
    * @param string|int $ttl
    * @param string $namespace
    * @return void
    */
    public function renewCache(string $cacheKey, string|int $ttl = 3600, string $namespace = ''): void
    {
        $this->mutator->renewCache($cacheKey, $ttl, $namespace);
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
        return $this->retriever->remember($cacheKey, $ttl, $callback);
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
        return $this->retriever->rememberForever($cacheKey, $callback);
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

    public function syncState(): void
    {
        $this->setMessage($this->cacheStore->getMessage(), $this->cacheStore->isSuccess());
    }

    public function setInternalState(string $message, bool $success): void
    {
        $this->setMessage($message, $success);
    }

    public function isFormatted(): bool
    {
        return $this->formatted;
    }

    public function isCompressionEnabled(): bool
    {
        return $this->compression;
    }

    public function getEncryptionKey(): ?string
    {
        return $this->encryptionKey;
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

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

    public function __construct(array $options = [], $formatted = false)
    {
        $this->formatted = $formatted;
        $this->validateOptions($options);
        $this->setDriver()->useDefaultDriver();
    }

    /**
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
     * @return void
     */
    public function flushCache()
    {
        $this->cacheStore->flushCache();
        $this->setMessage($this->cacheStore->getMessage(), $this->cacheStore->isSuccess());
    }

    /**
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
     * @return boolean
     */
    public function isSuccess()
    {
        return $this->success;
    }

    /**
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
     * @param string $cacheKey
     * @param Closure $callback
     * @return mixed
     */
    public function rememberForever(string $cacheKey, Closure $callback)
    {
        return $this->remember($cacheKey, 31536000 * 1000, $callback);
    }

    /**
     * @return CacheConfig
     */
    public function setConfig()
    {
        return new CacheConfig($this);
    }

    /**
     * @return CacheDriver
     */
    public function setDriver()
    {
        return new CacheDriver($this);
    }

    /**
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
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return void
     */
    public function useFormatter()
    {
        $this->formatted = !$this->formatted;
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

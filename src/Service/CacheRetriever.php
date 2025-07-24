<?php

namespace Silviooosilva\CacheerPhp\Service;

use Closure;
use Silviooosilva\CacheerPhp\Cacheer;
use Silviooosilva\CacheerPhp\Helpers\CacheerHelper;
use Silviooosilva\CacheerPhp\Utils\CacheDataFormatter;

class CacheRetriever
{
    private Cacheer $cacheer;

    public function __construct(Cacheer $cacheer)
    {
        $this->cacheer = $cacheer;
    }

    public function getCache(string $cacheKey, string $namespace = '', int|string $ttl = 3600)
    {
        $cacheData = $this->cacheer->cacheStore->getCache($cacheKey, $namespace, $ttl);
        $this->cacheer->syncState();

        if ($this->cacheer->isSuccess() && ($this->cacheer->isCompressionEnabled() || $this->cacheer->getEncryptionKey() !== null)) {
            $cacheData = CacheerHelper::recoverFromStorage($cacheData, $this->cacheer->isCompressionEnabled(), $this->cacheer->getEncryptionKey());
        }

        return $this->cacheer->isFormatted() ? new CacheDataFormatter($cacheData) : $cacheData;
    }

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

    public function rememberForever(string $cacheKey, Closure $callback)
    {
        return $this->remember($cacheKey, 31536000 * 1000, $callback);
    }

    public function has(string $cacheKey, string $namespace = ''): void
    {
        $this->cacheer->cacheStore->has($cacheKey, $namespace);
        $this->cacheer->syncState();
    }
}

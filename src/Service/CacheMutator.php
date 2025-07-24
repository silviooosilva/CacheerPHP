<?php

namespace Silviooosilva\CacheerPhp\Service;

use Silviooosilva\CacheerPhp\Cacheer;
use Silviooosilva\CacheerPhp\Helpers\CacheerHelper;

class CacheMutator
{
    private Cacheer $cacheer;

    public function __construct(Cacheer $cacheer)
    {
        $this->cacheer = $cacheer;
    }

    public function add(string $cacheKey, mixed $cacheData, string $namespace = '', int|string $ttl = 3600)
    {
        if (!empty($this->cacheer->getCache($cacheKey, $namespace))) {
            return true;
        }

        $this->putCache($cacheKey, $cacheData, $namespace, $ttl);
        $this->cacheer->setInternalState($this->cacheer->getMessage(), $this->cacheer->isSuccess());

        return false;
    }

    public function appendCache(string $cacheKey, mixed $cacheData, string $namespace = ''): void
    {
        $this->cacheer->cacheStore->appendCache($cacheKey, $cacheData, $namespace);
        $this->cacheer->syncState();
    }

    public function clearCache(string $cacheKey, string $namespace = ''): void
    {
        $this->cacheer->cacheStore->clearCache($cacheKey, $namespace);
        $this->cacheer->syncState();
    }

    public function decrement(string $cacheKey, int $amount = 1, string $namespace = '')
    {
        return $this->increment($cacheKey, ($amount * -1), $namespace);
    }

    public function forever(string $cacheKey, mixed $cacheData): void
    {
        $this->putCache($cacheKey, $cacheData, ttl: 31536000 * 1000);
        $this->cacheer->setInternalState($this->cacheer->getMessage(), $this->cacheer->isSuccess());
    }

    public function flushCache(): void
    {
        $this->cacheer->cacheStore->flushCache();
        $this->cacheer->syncState();
    }

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

    public function putCache(string $cacheKey, mixed $cacheData, string $namespace = '', int|string $ttl = 3600): void
    {
        $data = CacheerHelper::prepareForStorage($cacheData, $this->cacheer->isCompressionEnabled(), $this->cacheer->getEncryptionKey());
        $this->cacheer->cacheStore->putCache($cacheKey, $data, $namespace, $ttl);
        $this->cacheer->syncState();
    }

    public function putMany(array $items, string $namespace = '', int $batchSize = 100): void
    {
        $this->cacheer->cacheStore->putMany($items, $namespace, $batchSize);
    }

    public function renewCache(string $cacheKey, int|string $ttl = 3600, string $namespace = ''): void
    {
        $this->cacheer->cacheStore->renewCache($cacheKey, $ttl, $namespace);
        $this->cacheer->syncState();
    }
}

<?php

namespace Silviooosilva\CacheerPhp\CacheStore\Support;

use Silviooosilva\CacheerPhp\CacheStore\FileCacheStore;
use Silviooosilva\CacheerPhp\Helpers\CacheFileHelper;

class FileCacheBatchProcessor
{
    public function __construct(private FileCacheStore $store)
    {
    }

    public function process(array $batchItems, string $namespace): void
    {
        foreach ($batchItems as $item) {
            CacheFileHelper::validateCacheItem($item);
            $cacheKey = $item['cacheKey'];
            $cacheData = $item['cacheData'];
            $mergedData = CacheFileHelper::mergeCacheData($cacheData);
            $this->store->putCache($cacheKey, $mergedData, $namespace);
        }
    }
}

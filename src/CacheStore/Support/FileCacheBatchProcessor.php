<?php

namespace Silviooosilva\CacheerPhp\CacheStore\Support;

use Silviooosilva\CacheerPhp\CacheStore\FileCacheStore;
use Silviooosilva\CacheerPhp\Helpers\CacheFileHelper;

/**
 * Class FileCacheBatchProcessor
 * @author Sílvio Silva <https://github.com/silviooosilva>
 * @package Silviooosilva\CacheerPhp
 */
class FileCacheBatchProcessor
{

    /**
     * FileCacheBatchProcessor constructor.
     *
     * @param FileCacheStore $store
     */
    public function __construct(private FileCacheStore $store)
    {
    }
    
    /**
     * Processes a batch of cache items and stores them.
     *
     * @param array $batchItems
     * @param string $namespace
     * @return void
     */
    public function process(array $batchItems, string $namespace)
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

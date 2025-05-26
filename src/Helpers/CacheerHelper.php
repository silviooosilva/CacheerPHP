<?php

namespace Silviooosilva\CacheerPhp\Helpers;

use InvalidArgumentException;

class CacheerHelper
{
    /**
     * @param array $item
     * @param callable|null $exceptionFactory
     * @return void
     */
    public static function validateCacheItem(array $item, ?callable $exceptionFactory = null)
    {
        if (!isset($item['cacheKey']) || !isset($item['cacheData'])) {
            if ($exceptionFactory) {
                throw $exceptionFactory("Each item must contain 'cacheKey' and 'cacheData'");
            }
            throw new InvalidArgumentException("Each item must contain 'cacheKey' and 'cacheData'");
        }
    }

    /**
     * @param mixed $cacheData
     * @return array
     */
    public static function mergeCacheData($cacheData)
    {
        if (is_array($cacheData) && is_array(reset($cacheData))) {
            $merged = [];
            foreach ($cacheData as $data) {
                $merged[] = $data;
            }
            return $merged;
        }
        return (array)$cacheData;
    }

    /**
     * @param mixed $currentCacheData
     * @param mixed $cacheData
     * @return array
     */
    public static function arrayIdentifier(mixed $currentCacheData, mixed $cacheData)
    {
        if (is_array($currentCacheData) && is_array($cacheData)) {
            return array_merge($currentCacheData, $cacheData);
        }
        return array_merge((array)$currentCacheData, (array)$cacheData);
    }
}
<?php

namespace Silviooosilva\CacheerPhp\Helpers;

use Silviooosilva\CacheerPhp\Exceptions\CacheFileException;

/**
 * Class CacheFileHelper
 * @author Sílvio Silva <https://github.com/silviooosilva>
 * @package Silviooosilva\CacheerPhp
 */
class CacheFileHelper
{

    /**
    * @param string $expiration
    * @return int
    */
    public static function convertExpirationToSeconds(string $expiration)
    {
        $units = [
            'second' => 1,
            'minute' => 60,
            'hour'   => 3600,
            'day'    => 86400,
            'week'   => 604800,
            'month'  => 2592000,
            'year'   => 31536000,
        ];
        foreach ($units as $unit => $value) {
            if (strpos($expiration, $unit) !== false) {
                return (int)$expiration * $value;
            }
        }
        throw CacheFileException::create("Invalid expiration format");
    }

    /**
     * @param array $options
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
     * @param array $item
     * @return void
     */
    public static function validateCacheItem(array $item)
    {
        if (!isset($item['cacheKey']) || !isset($item['cacheData'])) {
            throw CacheFileException::create("Each item must contain 'cacheKey' and 'cacheData'");
        }
    }

    /**
    * @param string|int $ttl
    * @param int $defaultTTL
    * @return mixed
    */
    public static function ttl($ttl = null, ?int $defaultTTL = null) {
        if ($ttl) {
            $ttl = is_string($ttl) ? CacheFileHelper::convertExpirationToSeconds($ttl) : $ttl;
        } else {
            $ttl = $defaultTTL;
        }
        return $ttl;
    }

  /**
  * @param mixed $currentCacheData
  * @param mixed $cacheData
  * @return array
  */
  public static function arrayIdentifier(mixed $currentCacheData, mixed $cacheData)
  {
    if (is_array($currentCacheData) && is_array($cacheData)) {
      $mergedCacheData = array_merge($currentCacheData, $cacheData);
    } else {
      $mergedCacheData = array_merge((array)$currentCacheData, (array)$cacheData);
    }

    return $mergedCacheData;
  }
}

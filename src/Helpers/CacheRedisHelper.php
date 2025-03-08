<?php

namespace Silviooosilva\CacheerPhp\Helpers;

use Silviooosilva\CacheerPhp\Exceptions\CacheRedisException;

/**
 * Class CacheRedisHelper
 * @author Sílvio Silva <https://github.com/silviooosilva>
 * @package Silviooosilva\CacheerPhp
 */
class CacheRedisHelper
{

  /**
  * @param mixed $data
  * @param bool  $serialize
  * @return string
  */
  public static function serialize(mixed $data, bool $serialize = true)
  {
    if($serialize) {
      return serialize($data);
    }

    return unserialize($data);

  }

    /**
     * @param array $item
     * @return void
     */
    public static function validateCacheItem(array $item)
    {
        if (!isset($item['cacheKey']) || !isset($item['cacheData'])) {
            throw CacheRedisException::create("Each item must contain 'cacheKey' and 'cacheData'");
        }
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


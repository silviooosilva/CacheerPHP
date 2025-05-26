<?php

namespace Silviooosilva\CacheerPhp\Helpers;

use Silviooosilva\CacheerPhp\Helpers\CacheerHelper;
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
  * @return mixed
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
        CacheerHelper::validateCacheItem(
            $item,
            fn($msg) => CacheRedisException::create($msg)
        );
    }

    /**
     * @param array $options
     * @return array
     */
    public static function mergeCacheData($cacheData)
    {
        return CacheerHelper::mergeCacheData($cacheData);
    }

  /**
    * @param mixed $currentCacheData
    * @param mixed $cacheData
    * @return array
    */
  public static function arrayIdentifier(mixed $currentCacheData, mixed $cacheData)
  {
      return CacheerHelper::arrayIdentifier($currentCacheData, $cacheData);
  }

}


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
      /**
      * Se ambos forem arrays, mescle-os de forma recursiva para preservar subarrays
      */
      if (is_array($currentCacheData) && is_array($cacheData)) {
          return self::mergeRecursive($currentCacheData, $cacheData);
      }

      /** 
      * Se $currentCacheData não for um array, inicialize-o como um array vazio
      */
      if (!is_array($currentCacheData)) {
          $currentCacheData = [];
      }

      /**
      * Se $cacheData não for um array, converta-o em um array
      */
      if (!is_array($cacheData)) {
          $cacheData = [$cacheData];
      }

      return array_merge($currentCacheData, $cacheData);
  }

  /**
    * Mescla arrays de forma recursiva.
    * @param array $array1
    * @param array $array2
    * @return array
    */
  private static function mergeRecursive(array $array1, array $array2)
  {
      foreach ($array2 as $key => $value) {

          /**
          * Se a chave existe em ambos os arrays e ambos os valores são arrays, mescle recursivamente
          */
          if (isset($array1[$key]) && is_array($array1[$key]) && is_array($value)) {
              $array1[$key] = self::mergeRecursive($array1[$key], $value);
          } else {

              /**
              * Caso contrário, sobrescreva o valor em $array1 com o valor de $array2
              */
              $array1[$key] = $value;
          }
      }

      return $array1;
  }

}


<?php

namespace Silviooosilva\CacheerPhp\CacheStore;

use Silviooosilva\CacheerPhp\Utils\CacheLogger;
use Silviooosilva\CacheerPhp\Interface\CacheerInterface;

/**
 * Class ArrayCacheStore
 * @author Sílvio Silva <https://github.com/silviooosilva>
 * @package Silviooosilva\CacheerPhp
 */
class ArrayCacheStore implements CacheerInterface
{

  /**
  * @param array $arrayStore
  */
  private array $arrayStore = [];

  /**
   * @param boolean
   */
  private bool $success = false;

  /**
   * @param string
   */
  private string $message = '';

  /**
   * @var CacheLogger
   */
  private $logger = null;

  public function __construct(string $logPath)
  {
    $this->logger = new CacheLogger($logPath);
  }

  /**
   * @param string $cacheKey
   * @param mixed  $cacheData
   * @param string $namespace
   * @return bool
   */
  public function appendCache(string $cacheKey, mixed $cacheData, string $namespace = '')
  {
      $arrayStoreKey = $this->buildArrayKey($cacheKey, $namespace);

      if (!$this->has($cacheKey, $namespace)) {
          $this->setMessage("cacheData can't be appended, because doesn't exist or expired", false);
          $this->logger->debug("{$this->getMessage()} from array driver.");
          return false;
      }

      $this->arrayStore[$arrayStoreKey]['cacheData'] = serialize($cacheData);
      $this->setMessage("Cache appended successfully", true);
      return true;
  }

  /**
   * @param string $cacheKey
   * @param string $namespace
   * @return string
   */
  private function buildArrayKey(string $cacheKey, string $namespace = '')
  {
    return !empty($namespace) ? ($namespace . ':' . $cacheKey) : $cacheKey;
  }

  /**
   * @param string $cacheKey
   * @param string $namespace
   * @return void
   */
  public function clearCache(string $cacheKey, string $namespace = '')
  {
    $arrayStoreKey = $this->buildArrayKey($cacheKey, $namespace);
    unset($this->arrayStore[$arrayStoreKey]);
    $this->setMessage("Cache cleared successfully", true);
    $this->logger->debug("{$this->getMessage()} from array driver.");
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
   * @return void
   */
  public function flushCache()
  {
    unset($this->arrayStore);
    $this->arrayStore = [];
    $this->setMessage("Cache flushed successfully", true);
    $this->logger->debug("{$this->getMessage()} from array driver.");
  }

  /**
   * @param string $cacheKey
   * @param mixed $cacheData
   * @param string $namespace
   * @param int|string $ttl
   * @return void
   */
  public function forever(string $cacheKey, mixed $cacheData)
  {
    $this->putCache($cacheKey, $cacheData, ttl: 31536000 * 1000);
    $this->setMessage($this->getMessage(), $this->isSuccess());
  }

  /**
   * @param string $cacheKey
   * @param string $namespace
   * @param int|string $ttl
   * @return mixed
   */
  public function getCache(string $cacheKey, string $namespace = '', string|int $ttl = 3600)
  {
    $arrayStoreKey = $this->buildArrayKey($cacheKey, $namespace);

    if (!$this->has($cacheKey, $namespace)) {
      $this->setMessage("cacheData not found, does not exists or expired", false);
      $this->logger->debug("{$this->getMessage()} from array driver.");
      return false;
    }

    $cacheData = $this->arrayStore[$arrayStoreKey];
    $expirationTime = $cacheData['expirationTime'] ?? 0;
    $now = time();

    if($expirationTime !== 0 && $now >= $expirationTime) {
      list($np, $key) = explode(':', $arrayStoreKey);
      $this->clearCache($key, $np);
      $this->setMessage("cacheKey: {$key} has expired.", false);
      $this->logger->debug("{$this->getMessage()} from array driver.");
      return false;
    }

    $this->setMessage("Cache retrieved successfully", true);
    $this->logger->debug("{$this->getMessage()} from array driver.");
    return $this->serialize($cacheData['cacheData'], false);
  }

  /**
   * @param string $cacheKey
   * @param string $namespace
   * @return bool
   */
  public function has(string $cacheKey, string $namespace = '')
  {
    $arrayStoreKey = $this->buildArrayKey($cacheKey, $namespace);
    return isset($this->arrayStore[$arrayStoreKey]) && time() < $this->arrayStore[$arrayStoreKey]['expirationTime'];
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
   * @return string
   */
  public function getMessage()
  {
    return $this->message;
  }

  /**
   * @param string $cacheKey
   * @param mixed $cacheData
   * @param string $namespace
   * @param int|string $ttl
   * @return bool
   */
  public function putCache(string $cacheKey, mixed $cacheData, string $namespace = '', int|string $ttl = 3600)
  {
    $arrayStoreKey = $this->buildArrayKey($cacheKey, $namespace);

    $this->arrayStore[$arrayStoreKey] = [
      'cacheData' => serialize($cacheData),
      'expirationTime' => time() + $ttl
    ];

    $this->setMessage("Cache stored successfully", true);
    $this->logger->debug("{$this->getMessage()} from Array driver.");
    return true;
  }

  /**
   * @param array $items
   * @param string $namespace
   * @param int $batchSize
   * @return void
   */
  public function putMany(array $items, string $namespace = '', int $batchSize = 100)
  {
    $chunks = array_chunk($items, $batchSize, true);

    foreach ($chunks as $chunk) {
      foreach ($chunk as $key => $data) {
          $this->putCache($data['cacheKey'], $data['cacheData'], $namespace);
        }
      }
    $this->setMessage("{$this->getMessage()}", $this->isSuccess());
    $this->logger->debug("{$this->getMessage()} from Array driver.");
  }

  /**
   * @param string $cacheKey
   * @param string|int $ttl
   * @param string $namespace
   * @return void
   */
  public function renewCache(string $cacheKey, int|string $ttl = 3600, string $namespace = '')
  {
    $arrayStoreKey = $this->buildArrayKey($cacheKey, $namespace);

    if (isset($this->arrayStore[$arrayStoreKey])) {
        $ttlSeconds = is_numeric($ttl) ? (int) $ttl : strtotime($ttl) - time();
        $this->arrayStore[$arrayStoreKey]['expirationTime'] = time() + $ttlSeconds;
        $this->setMessage("cacheKey: {$cacheKey} renewed successfully", true);
        $this->logger->debug("{$this->getMessage()} from array driver.");
      }
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
   * @param mixed $data
   * @param bool $serialize
   * @return mixed
   */
  private function serialize(mixed $data, bool $serialize = true)
  {
    return $serialize ? serialize($data) : unserialize($data);
  }
}

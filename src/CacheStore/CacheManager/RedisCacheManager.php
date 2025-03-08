<?php

namespace Silviooosilva\CacheerPhp\CacheStore\CacheManager;

use Predis\Client;
use Predis\Autoloader;

class RedisCacheManager 
{

  /** @var Predis\Client */
  private static $redis;

  /** @param string $namespace */
  private static $namespace;

  /**
  * @return Client
  */
  public static function connect()
  {
    Autoloader::register();
    self::$redis = new Client([
      'scheme' => 'tcp',
      'host' => REDIS_CONNECTION_CONFIG['REDIS_HOST'],
      'port' => REDIS_CONNECTION_CONFIG['REDIS_PORT'],
      'password' => REDIS_CONNECTION_CONFIG['REDIS_PASSWORD'],
      'database' => 0
    ]);
    self::auth();
    self::$namespace = REDIS_CONNECTION_CONFIG['REDIS_NAMESPACE'] ?? 'Cache';
    return self::$redis;
  }

  /**
  * @return void
  */
  private static function auth()
  {
    if(is_string(REDIS_CONNECTION_CONFIG['REDIS_PASSWORD']) && REDIS_CONNECTION_CONFIG['REDIS_PASSWORD'] !== '') {
      self::$redis->auth(REDIS_CONNECTION_CONFIG['REDIS_PASSWORD']);
    }
  }

}
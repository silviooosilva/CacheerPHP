<?php

use PHPUnit\Framework\TestCase;
use Silviooosilva\CacheerPhp\Cacheer;
use Silviooosilva\CacheerPhp\CacheStore\RedisCacheStore;

class RedisTest extends TestCase
{

  /** @var Cacheer */
  private $cache;

  protected function setUp(): void
  {
    $this->cache = new Cacheer();
    $this->cache->setDriver()->useRedisDriver();
  }

  protected function tearDown(): void
  {
    $this->cache->flushCache();
  }

  public function testUsingRedisDriverSetsProperInstance()
  {
    $this->assertInstanceOf(RedisCacheStore::class, $this->cache->cacheStore);
  }

  public function testPutCacheInRedis()
  {
    $cacheKey = 'test_key';
    $cacheData = ['name' => 'Sílvio Silva', 'role' => 'Developer'];

    $this->cache->putCache($cacheKey, $cacheData);

    $this->assertEquals("Cache stored successfully", $this->cache->getMessage());
    $this->assertNotEmpty($this->cache->getCache($cacheKey));
    $this->assertEquals($cacheData, $this->cache->getCache($cacheKey));

  }

  public function testGetCacheFromRedis()
  {
    $cacheKey = 'test_key';
    $cacheData = ['name' => 'Sílvio Silva', 'role' => 'Developer'];

    $this->cache->putCache($cacheKey, $cacheData);
    
    $this->assertEquals("Cache stored successfully", $this->cache->getMessage());

    $data = $this->cache->getCache($cacheKey);
    $this->assertNotEmpty($data);
    $this->assertEquals($cacheData, $data);
  }

  public function testExpiredCacheInRedis()
  {
    $cacheKey = 'expired_key';
    $cacheData = ['name' => 'Expired User', 'email' => 'expired@example.com'];

    $this->cache->putCache($cacheKey, $cacheData, '', 1);
    sleep(3);

    $this->assertEquals("Cache stored successfully", $this->cache->getMessage());
    $this->assertEmpty($this->cache->getCache($cacheKey));
    $this->assertFalse($this->cache->isSuccess());
  }

  public function testOverwriteExistingCacheInRedis()
  {
    $cacheKey = 'overwrite_key';
    $initialCacheData = ['name' => 'Initial Data', 'email' => 'initial@example.com'];
    $newCacheData = ['name' => 'New Data', 'email' => 'new@example.com'];

    $this->cache->putCache($cacheKey, $initialCacheData);
    $this->assertEquals("Cache stored successfully", $this->cache->getMessage());

    $this->cache->appendCache($cacheKey, $newCacheData);
    $this->assertEquals("Cache appended successfully", $this->cache->getMessage());
    $this->assertEquals($newCacheData, $this->cache->getCache($cacheKey));
  }

  public function testPutManyCacheItemsInRedis()
  {
     $items = [
            [
                'cacheKey' => 'user_1_profile',
                'cacheData' => [
                    ['name' => 'John Doe', 'email' => 'john@example.com'],
                    ['name' => 'John Doe', 'email' => 'john@example.com'],
                    ['name' => 'John Doe', 'email' => 'john@example.com'],
                    ['name' => 'John Doe', 'email' => 'john@example.com']
                ]
            ],
            [
                'cacheKey' => 'user_2_profile',
                'cacheData' => [
                    ['name' => 'Jane Doe', 'email' => 'jane@example.com'],
                    ['name' => 'Jane Doe', 'email' => 'jane@example.com'],
                    ['name' => 'Jane Doe', 'email' => 'jane@example.com'],
                    ['name' => 'Jane Doe', 'email' => 'jane@example.com']
                ]
            ]
        ];

    $this->cache->putMany($items);
    foreach ($items as $item) {
            $this->assertEquals($item['cacheData'], $this->cache->getCache($item['cacheKey']));
        }
  }

      public function testAppendCacheWithNamespaceInRedis()
    {
        $cacheKey = 'test_append_key_ns';
        $namespace = 'test_namespace';

        $initialData = ['initial' => 'data'];
        $additionalData = ['new' => 'data'];

        $expectedData = array_merge($initialData, $additionalData);

 
        $this->cache->putCache($cacheKey, $initialData, $namespace);
        $this->assertTrue($this->cache->isSuccess());

 
        $this->cache->appendCache($cacheKey, $additionalData, $namespace);
        $this->assertTrue($this->cache->isSuccess());


        $cachedData = $this->cache->getCache($cacheKey, $namespace);
        $this->assertEquals($expectedData, $cachedData);
    }

      public function testDataOutputShouldBeOfTypeArray()
    {

        $this->cache->useFormatter();

        $cacheKey = "key_array";
        $cacheData = "data_array";

        $this->cache->putCache($cacheKey, $cacheData);
        $this->assertTrue($this->cache->isSuccess());

        $cacheOutput = $this->cache->getCache($cacheKey)->toArray();
        $this->assertTrue($this->cache->isSuccess());
        $this->assertIsArray($cacheOutput);
    }

    public function testDataOutputShouldBeOfTypeObject()
    {
        $this->cache->useFormatter();

        $cacheKey = "key_object";
        $cacheData = ["id" => 123];

        $this->cache->putCache($cacheKey, $cacheData);
        $this->assertTrue($this->cache->isSuccess());

        $cacheOutput = $this->cache->getCache($cacheKey)->toObject();
        $this->assertTrue($this->cache->isSuccess());
        $this->assertIsObject($cacheOutput);
    }

        public function testDataOutputShouldBeOfTypeJson()
    {
        $this->cache->useFormatter();

        $cacheKey = "key_json";
        $cacheData = "data_json";

        $this->cache->putCache($cacheKey, $cacheData);
        $this->assertTrue($this->cache->isSuccess());

        $cacheOutput = $this->cache->getCache($cacheKey)->toJson();
        $this->assertTrue($this->cache->isSuccess());
        $this->assertJson($cacheOutput);
    }

      public function testClearCacheDataFromRedis()
    {
        $cacheKey = 'test_key';
        $data = 'test_data';

        $this->cache->putCache($cacheKey, $data);
        $this->assertEquals("Cache stored successfully", $this->cache->getMessage());

        $this->cache->clearCache($cacheKey);
        $this->assertTrue($this->cache->isSuccess());
        $this->assertEquals("Cache cleared successfully", $this->cache->getMessage());

        $this->assertEmpty($this->cache->getCache($cacheKey));
    }

  public function testFlushCacheDataFromDatabase()
    {
        $key1 = 'test_key1';
        $data1 = 'test_data1';

        $key2 = 'test_key2';
        $data2 = 'test_data2';

        $this->cache->putCache($key1, $data1);
        $this->cache->putCache($key2, $data2);
        $this->assertTrue($this->cache->isSuccess());
        $this->assertTrue($this->cache->isSuccess());

        $this->cache->flushCache();

        $this->assertTrue($this->cache->isSuccess());
        $this->assertEquals("Cache flushed successfully", $this->cache->getMessage());
    }

  public function testHasCacheFromRedis()
  {
    $cacheKey = 'test_key';
    $cacheData = ['name' => 'Sílvio Silva', 'role' => 'Developer'];

    $this->cache->putCache($cacheKey, $cacheData);

    $this->assertEquals("Cache stored successfully", $this->cache->getMessage());
    $this->assertTrue($this->cache->isSuccess());
  }

  public function testRenewCacheFromRedis()
  {
    $cacheKey = 'expired_key';
    $cacheData = ['name' => 'Expired User', 'email' => 'expired@example.com'];

    // Define TTL de 10 seg para que a chave ainda exista quando renovarmos
    $this->cache->putCache($cacheKey, $cacheData, '', 120);
    $this->assertEquals("Cache stored successfully", $this->cache->getMessage());
    sleep(2);

    // Verifica que a chave existe antes de renovar
    $this->assertNotEmpty($this->cache->getCache($cacheKey));

    $this->cache->renewCache($cacheKey, 7200);
    $this->assertTrue($this->cache->isSuccess());
    $this->assertNotEmpty($this->cache->getCache($cacheKey));
  }

    public function testRenewCacheWithNamespaceFromRedis()
  {
    $cacheKey = 'expired_key';
    $namespace = 'expired_namespace';
    $cacheData = ['name' => 'Expired User', 'email' => 'expired@example.com'];

    $this->cache->putCache($cacheKey, $cacheData, $namespace, 120);
    $this->assertEquals("Cache stored successfully", $this->cache->getMessage());
    sleep(2);

    $this->assertNotEmpty($this->cache->getCache($cacheKey, $namespace));

    $this->cache->renewCache($cacheKey, 7200, $namespace);
    $this->assertTrue($this->cache->isSuccess());
    $this->assertNotEmpty($this->cache->getCache($cacheKey, $namespace));
  }

}

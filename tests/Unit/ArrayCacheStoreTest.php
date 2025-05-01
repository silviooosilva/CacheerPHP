<?php

use PHPUnit\Framework\TestCase;
use Silviooosilva\CacheerPhp\Cacheer;
use Silviooosilva\CacheerPhp\CacheStore\ArrayCacheStore;

class ArrayCacheStoreTest extends TestCase
{

  private $cache;

  protected function setUp(): void
  {
    $this->cache = new Cacheer();
    $this->cache->setDriver()->useArrayDriver();
    $this->cache->setConfig()->setTimeZone('America/Toronto');
  }

  protected function tearDown(): void
  {
    $this->cache->flushCache();
  }

    public function testUsingArrayDriverSetsProperInstance()
  {
    $this->assertInstanceOf(ArrayCacheStore::class, $this->cache->cacheStore);
  }

  public function testPutAndGetCacheInArray()
  {
      $cacheKey = 'test_key';
      $cacheData = ['name' => 'John Doe', 'email' => 'john@example.com'];
      
      $this->cache->putCache($cacheKey, $cacheData, '', 3600);

      $result = $this->cache->getCache($cacheKey);

      $this->assertNotEmpty($result);
      $this->assertEquals($cacheData, $result);
  }

  public function testExpiredCacheInArray()
  {
    $cacheKey = 'expired_key';
    $cacheData = ['name' => 'Expired User', 'email' => 'expired@example.com'];

    $this->cache->putCache($cacheKey, $cacheData, '', 1);
    sleep(3);

    $this->assertEquals("Cache stored successfully", $this->cache->getMessage());
    $this->assertEmpty($this->cache->getCache($cacheKey));
    $this->assertFalse($this->cache->isSuccess());
  }


  public function testOverwriteExistingCacheInArray()
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


  public function testPutManyCacheItemsInArray()
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

  public function testHasCacheFromArray()
  {
    $cacheKey = 'test_key';
    $cacheData = ['name' => 'Sílvio Silva', 'role' => 'Developer'];

    $this->cache->putCache($cacheKey, $cacheData);

    $this->assertEquals("Cache stored successfully", $this->cache->getMessage());
    $this->assertTrue($this->cache->isSuccess());

    $this->cache->has($cacheKey);
    $this->assertTrue($this->cache->isSuccess());
  }

    public function testRenewCacheFromArray()
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

      public function testRenewCacheWithNamespaceFromArray()
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

    public function testClearCacheDataFromArray()
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

    public function testFlushCacheDataFromArray()
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

  public function test_remember_saves_and_recover_values() 
    {
        $this->cache->flushCache();

        $value = $this->cache->remember('remember_test_key', 60, function () {
            return 'valor_teste';
        });

        $this->assertEquals('valor_teste', $value);

        $cachedValue = $this->cache->remember('remember_test_key', 60, function (){
            return 'novo_valor';
        });


        $this->assertEquals('valor_teste', $cachedValue);
    }

    public function test_remember_forever_saves_value_indefinitely()
    {
        $this->cache->flushCache();

        $value = $this->cache->rememberForever('remember_forever_key', function () {
            return 'valor_eterno';
        });
        $this->assertEquals('valor_eterno', $value);

        $cachedValue = $this->cache->rememberForever('remember_forever_key', function () {
            return 'novo_valor';
        });

        $this->assertEquals('valor_eterno', $cachedValue);
    }


      public function test_get_and_forget()
    {
        $cacheKey = 'cache_key_test';
        $this->cache->putCache($cacheKey, 10);

        $this->assertTrue($this->cache->isSuccess());

        $cacheData = $this->cache->getAndForget($cacheKey);

        $this->assertTrue($this->cache->isSuccess());
        $this->assertEquals(10, $cacheData);

        $oldCacheData = $this->cache->getAndForget($cacheKey);

        $this->assertNull($oldCacheData);
        $this->assertFalse($this->cache->isSuccess());

        $noCacheData = $this->cache->getAndForget('non_existent_cache_key');
        $this->assertNull($noCacheData);
    }

      public function test_store_if_not_present_with_add_function()
    {
        $existentKey = 'cache_key_test';

        $nonExistentKey = 'non_existent_key';

        $this->cache->putCache($existentKey, 'existent_data');

        $this->assertTrue($this->cache->isSuccess());
        $this->assertEquals('existent_data', $this->cache->getCache($existentKey));

        $addCache = $this->cache->add($existentKey, 100);
        
        $this->assertTrue($addCache);
        $this->assertNotEquals(100, 'existent_data');
    
        $addNonExistentKey = $this->cache->add($nonExistentKey, 'non_existent_data');

        $this->assertFalse($addNonExistentKey);
        $this->assertEquals('non_existent_data', $this->cache->getCache($nonExistentKey));
        $this->assertTrue($this->cache->isSuccess());

    }

      public function test_increment_function() {

        $cacheKey = 'test_increment';
        $cacheData = 2025;

        $this->cache->putCache($cacheKey, $cacheData);

        $this->assertTrue($this->cache->isSuccess());
        $this->assertEquals($cacheData, $this->cache->getCache($cacheKey));
        $this->assertIsNumeric($this->cache->getCache($cacheKey));

        $increment = $this->cache->increment($cacheKey, 2);
        $this->assertTrue($increment);

        $this->assertEquals(2027, $this->cache->getCache($cacheKey));

    }

        public function test_decrement_function() {

        $cacheKey = 'test_decrement';
        $cacheData = 2027;

        $this->cache->putCache($cacheKey, $cacheData);

        $this->assertTrue($this->cache->isSuccess());
        $this->assertEquals($cacheData, $this->cache->getCache($cacheKey));
        $this->assertIsNumeric($this->cache->getCache($cacheKey));

        $increment = $this->cache->decrement($cacheKey, 2);
        $this->assertTrue($increment);

        $this->assertEquals(2025, $this->cache->getCache($cacheKey));

    }

}
<?php

use PHPUnit\Framework\TestCase;
use Silviooosilva\CacheerPhp\Cacheer;
use Silviooosilva\CacheerPhp\Core\Connect;

class CacheerDatabaseTest extends TestCase
{
    private $cache;

    protected function setUp(): void
    {
        $this->cache = new Cacheer();
        $this->cache->setConfig()->setDatabaseConnection('mysql');
        $this->cache->setDriver()->useDatabaseDriver();
        $this->cache->setConfig()->setTimeZone('Africa/Luanda');
    }

    protected function tearDown(): void
    {
        $this->cache->flushCache();
    }

    public function testPutCacheInDatabase()
    {
        $cacheKey = 'test_key';
        $cacheData = ['name' => 'John Doe', 'email' => 'john@example.com'];

        $this->cache->putCache($cacheKey, $cacheData, '', 3600);

        $result = $this->cache->getCache($cacheKey);

        $this->assertNotEmpty($result);
        $this->assertEquals($cacheData, $result);
    }

    public function testGetCacheFromDatabase()
    {
        $cacheKey = 'test_key02';
        $cacheData = ['name' => 'Jane Doe', 'email' => 'jane@example.com'];

        $this->cache->putCache($cacheKey, $cacheData, '', 3600);
        $this->assertEquals("Cache Stored Successfully", $this->cache->getMessage());

        $result = $this->cache->getCache($cacheKey);

        $this->assertNotEmpty($result);
        $this->assertEquals($cacheData, $result);
    }

    public function testExpiredCacheInDatabase()
    {
        $cacheKey = 'expired_key';
        $cacheData = ['name' => 'Expired User', 'email' => 'expired@example.com'];

        $this->cache->putCache($cacheKey, $cacheData, '', -3600);
        $this->assertEquals("Cache Stored Successfully", $this->cache->getMessage());

        $this->assertEmpty($this->cache->getCache($cacheKey));
        $this->assertFalse($this->cache->isSuccess());
    }
    public function testOverwriteExistingCacheInDatabase()
    {

        $cacheKey = 'overwrite_key';
        $initialCacheData = ['name' => 'Initial Data', 'email' => 'initial@example.com'];
        $newCacheData = ['name' => 'New Data', 'email' => 'new@example.com'];

        $expirationTime = date('Y-m-d H:i:s', time() + 3600);


        $db = Connect::getInstance();
        $query = $db->prepare("INSERT INTO cacheer_table (cacheKey, cacheData, cacheNamespace, expirationTime) VALUES (?, ?, ?, ?)");
        $query->bindValue(1, $cacheKey);
        $query->bindValue(2, serialize($initialCacheData));
        $query->bindValue(3, '');
        $query->bindValue(4, $expirationTime);

        $this->assertTrue($query->execute());

        $this->cache->appendCache($cacheKey, $newCacheData);
        $this->assertEquals("Cache updated successfully", $this->cache->getMessage());

        $query = $db->prepare("SELECT cacheData FROM cacheer_table WHERE cacheKey = ? AND cacheNamespace = ? AND expirationTime > NOW()");
        $query->bindValue(1, $cacheKey);
        $query->bindValue(2, '');

        $this->assertTrue($query->execute());

        $result = $query->fetch(PDO::FETCH_ASSOC);

        $this->assertEquals($newCacheData, unserialize($result['cacheData']));
    }

    public function testPutManyCacheItemsInDatabase(): void
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

    public function testAppendCacheWithNamespaceInDatabase(): void
    {
        $cacheKey = 'test_append_key_ns';
        $namespace = 'test_namespace';

        $initialData = ['initial' => 'data'];
        $additionalData = ['new' => 'data'];

        $expectedData = array_merge($initialData, $additionalData);

        // Armazena os dados iniciais no cache com namespace
        $this->cache->putCache($cacheKey, $initialData, $namespace);
        $this->assertTrue($this->cache->isSuccess());

        // Adiciona novos dados ao cache existente com namespace
        $this->cache->appendCache($cacheKey, $additionalData, $namespace);
        $this->assertTrue($this->cache->isSuccess());

        // Verifica se os dados no cache são os esperados
        $cachedData = $this->cache->getCache($cacheKey, $namespace);
        $this->assertEquals($expectedData, $cachedData);
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


    public function testClearCacheDataFromDatabase(): void
    {
        $cacheKey = 'test_key';
        $data = 'test_data';

        $this->cache->putCache($cacheKey, $data);
        $this->assertEquals("Cache Stored Successfully", $this->cache->getMessage());

        $this->cache->clearCache($cacheKey);
        $this->assertTrue($this->cache->isSuccess());
        $this->assertEquals("Cache deleted successfully!", $this->cache->getMessage());

        $this->assertEmpty($this->cache->getCache($cacheKey));
    }


    public function testFlushCacheDataFromDatabase(): void
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
        $this->assertEquals("Flush finished successfully", $this->cache->getMessage());
    }
}

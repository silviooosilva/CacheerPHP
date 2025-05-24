<?php

use PHPUnit\Framework\TestCase;
use Silviooosilva\CacheerPhp\Cacheer;
use Silviooosilva\CacheerPhp\Utils\CacheDriver;

class FileCacheStoreTest extends TestCase
{
    private $cache;
    private $cacheDir;

    protected function setUp(): void
    {
        $this->cacheDir = __DIR__ . '/cache';
        if (!file_exists($this->cacheDir) || !is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }

        $options = [
            'cacheDir' => $this->cacheDir,
        ];

        $this->cache = new Cacheer($options);
    }

    protected function tearDown(): void
    {
        $this->cache->flushCache();
    }

    public function testPutCache()
    {
        $cacheKey = 'test_key';
        $data = 'test_data';

        $this->cache->putCache($cacheKey, $data);
        $this->assertTrue($this->cache->isSuccess());
        $this->assertEquals('Cache file created successfully', $this->cache->getMessage());

        $cacheFile = $this->cacheDir . '/' . md5($cacheKey) . '.cache';
        $this->assertFileExists($cacheFile);
        $this->assertEquals($data, $this->cache->getCache($cacheKey));
    }

    public function testGetCache()
    {
        $cacheKey = 'test_key';
        $data = 'test_data';

        $this->cache->putCache($cacheKey, $data);

        $cachedData = $this->cache->getCache($cacheKey);
        $this->assertTrue($this->cache->isSuccess());
        $this->assertEquals($data, $cachedData);

        // Recuperar cache fora do período de expiração
        sleep(2);
        $cachedData = $this->cache->getCache($cacheKey, '', '2 seconds');
        $this->assertFalse($this->cache->isSuccess());
        $this->assertEquals('cacheFile not found, does not exists or expired', $this->cache->getMessage());
    }

    public function testClearCache()
    {
        $cacheKey = 'test_key';
        $data = 'test_data';

        $this->cache->putCache($cacheKey, $data);
        $this->cache->clearCache($cacheKey);

        $this->assertTrue($this->cache->isSuccess());
        $this->assertEquals('Cache file deleted successfully!', $this->cache->getMessage());

        $cacheFile = $this->cacheDir . '/' . md5($cacheKey) . '.cache';
        $this->assertFileDoesNotExist($cacheFile);
    }

    public function testFlushCache()
    {
        $key1 = 'test_key1';
        $data1 = 'test_data1';

        $key2 = 'test_key2';
        $data2 = 'test_data2';

        $this->cache->putCache($key1, $data1);
        $this->cache->putCache($key2, $data2);
        $this->cache->flushCache();

        $cacheFile1 = $this->cacheDir . '/' . md5($key1) . '.cache';
        $cacheFile2 = $this->cacheDir . '/' . md5($key2) . '.cache';

        $this->assertFileDoesNotExist($cacheFile1);
        $this->assertFileDoesNotExist($cacheFile2);
    }

    public function testAutoFlush()
    {
        $options = [
            'cacheDir' => $this->cacheDir,
            'flushAfter' => '10 seconds'
        ];

        $this->cache = new Cacheer($options);
        $this->cache->putCache('test_key', 'test_data');

        // Verifica se o cache foi criado com sucesso
        $this->assertEquals('test_data', $this->cache->getCache('test_key'));
        $this->assertTrue($this->cache->isSuccess());

        // Espera 11 segundos para o cache ser limpo automaticamente
        sleep(11);

        $this->cache = new Cacheer($options);

        // Verifica se o cache foi limpo automaticamente
        $cachedData = $this->cache->getCache('test_key');
        $this->assertFalse($this->cache->isSuccess());
        $this->assertEquals('cacheFile not found, does not exists or expired', $this->cache->getMessage());
    }

    public function testAppendCache()
    {
        $cacheKey = 'test_append_key';
        $initialData = ['initial' => 'data'];
        $additionalData = ['new' => 'data'];
        $expectedData = array_merge($initialData, $additionalData);

        // Armazena os dados iniciais no cache
        $this->cache->putCache($cacheKey, $initialData);
        $this->assertTrue($this->cache->isSuccess());

        // Adiciona novos dados ao cache existente
        $this->cache->appendCache($cacheKey, $additionalData);
        $this->assertTrue($this->cache->isSuccess());

        // Verifica se os dados no cache são os esperados
        $cachedData = $this->cache->getCache($cacheKey);
        $this->assertEquals($expectedData, $cachedData);

        // Testa adicionar dados como string
        $additionalData = ['string_data' => 'string data'];
        $expectedData = array_merge($expectedData, $additionalData);
        $this->cache->appendCache($cacheKey, $additionalData);
        $cachedData = $this->cache->getCache($cacheKey);
        $this->assertEquals($expectedData, $cachedData);
    }

    public function testAppendCacheFileNotExists()
    {
        $cacheKey = 'non_existing_key';
        $data = ['data'];

        // Tenta adicionar dados a um arquivo de cache que não existe
        $this->cache->appendCache($cacheKey, $data);
        $this->assertFalse($this->cache->isSuccess());
        $this->assertEquals('cacheFile not found, does not exists or expired', $this->cache->getMessage());
    }

    public function testAppendCacheWithNamespace()
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
        $options = [
            'cacheDir' => $this->cacheDir
        ];
        $this->cache = new Cacheer($options, true);

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
        $options = [
            'cacheDir' => $this->cacheDir
        ];
        $this->cache = new Cacheer($options, true);

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
        $options = [
            'cacheDir' => $this->cacheDir
        ];
        $this->cache = new Cacheer($options, true);

        $cacheKey = "key_object";
        $cacheData = ["id" => 123];

        $this->cache->putCache($cacheKey, $cacheData);
        $this->assertTrue($this->cache->isSuccess());

        $cacheOutput = $this->cache->getCache($cacheKey)->toObject();
        $this->assertTrue($this->cache->isSuccess());
        $this->assertIsObject($cacheOutput);
    }

    public function testPutMany()
    {
        $cacheer = new Cacheer(['cacheDir' => __DIR__ . '/cache']);
        $items = [
            [
                'cacheKey' => 'user_1_profile',
                'cacheData' => ['name' => 'John Doe', 'email' => 'john@example.com']
            ],
            [
                'cacheKey' => 'user_2_profile',
                'cacheData' => ['name' => 'Jane Doe', 'email' => 'jane@example.com']
            ],
        ];

        $cacheer->putMany($items);

        foreach ($items as $item) {
            $this->assertEquals($item['cacheData'], $cacheer->getCache($item['cacheKey']));
        }
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

        private function removeDirectoryRecursively($dir)
        {
        if (!is_dir($dir)) {
            return;
        }
        $items = scandir($dir);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
            continue;
            }
            $path = $dir . DIRECTORY_SEPARATOR . $item;
            if (is_dir($path)) {
            $this->removeDirectoryRecursively($path);
            } else {
            unlink($path);
            }
        }
        rmdir($dir);
        }

        public function testUseDefaultDriverCreatesCacheDirInProjectRoot()
        {
        $cacheer = new Cacheer();
        $driver = new CacheDriver($cacheer);

        $projectRoot = dirname(__DIR__, 2);
        $expectedCacheDir = $projectRoot . DIRECTORY_SEPARATOR . "CacheerPHP" . DIRECTORY_SEPARATOR . "Cache";

        if (is_dir($expectedCacheDir)) {
            $this->removeDirectoryRecursively($expectedCacheDir);
        }

        $driver->useDefaultDriver();

        $this->assertDirectoryExists($expectedCacheDir);

        if (is_dir($expectedCacheDir)) {
            $this->removeDirectoryRecursively($expectedCacheDir);
        }
        }

        public function testPutCacheWithNamespace()
        {
        $cacheKey = 'namespace_key';
        $data = 'namespace_data';
        $namespace = 'my_namespace';

        $this->cache->putCache($cacheKey, $data, $namespace);
        $this->assertTrue($this->cache->isSuccess());

        $cachedData = $this->cache->getCache($cacheKey, $namespace);
        $this->assertEquals($data, $cachedData);
        }

        public function testClearCacheWithNamespace()
        {
        $cacheKey = 'namespace_key_clear';
        $data = 'namespace_data_clear';
        $namespace = 'clear_namespace';

        $this->cache->putCache($cacheKey, $data, $namespace);
        $this->assertTrue($this->cache->isSuccess());

        $this->cache->clearCache($cacheKey, $namespace);
        $this->assertTrue($this->cache->isSuccess());

        $cachedData = $this->cache->getCache($cacheKey, $namespace);
        $this->assertFalse($this->cache->isSuccess());
        $this->assertNull($cachedData);
        }

        public function testFlushCacheRemovesNamespacedFiles()
        {
        $cacheKey = 'ns_flush_key';
        $data = 'ns_flush_data';
        $namespace = 'flush_namespace';

        $this->cache->putCache($cacheKey, $data, $namespace);
        $this->assertTrue($this->cache->isSuccess());

        $this->cache->flushCache();

        $cachedData = $this->cache->getCache($cacheKey, $namespace);
        $this->assertFalse($this->cache->isSuccess());
        $this->assertNull($cachedData);
        }

        public function testAppendCacheWithDifferentTypes()
        {
        $cacheKey = 'append_type_key';
        $initialData = ['a' => 1];
        $additionalData = ['b' => 2];
        $expectedData = ['a' => 1, 'b' => 2];

        $this->cache->putCache($cacheKey, $initialData);
        $this->cache->appendCache($cacheKey, $additionalData);
        $this->assertEquals($expectedData, $this->cache->getCache($cacheKey));

        $this->cache->appendCache($cacheKey, ['c' => 'string']);
        $expectedData['c'] = 'string';
        $this->assertEquals($expectedData, $this->cache->getCache($cacheKey));
        }

    }

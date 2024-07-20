<?php

use PHPUnit\Framework\TestCase;
use Silviooosilva\CacheerPhp\Cacheer;

class CacheerTest extends TestCase
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
            'expirationTime' => '1 second'
        ];

        $this->cache = new Cacheer($options);
    }

    protected function tearDown(): void
    {
        array_map('unlink', glob("$this->cacheDir/*.cache"));
        rmdir($this->cacheDir);
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
        $this->assertEquals($data, unserialize(file_get_contents($cacheFile)));
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
        $cachedData = $this->cache->getCache($cacheKey);
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
}

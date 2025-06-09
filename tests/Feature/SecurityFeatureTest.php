<?php

use PHPUnit\Framework\TestCase;
use Silviooosilva\CacheerPhp\Cacheer;

class SecurityFeatureTest extends TestCase
{
    private $cache;
    private $cacheDir;

    protected function setUp(): void
    {
        $this->cacheDir = __DIR__ . '/cache';
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }

        $this->cache = new Cacheer(['cacheDir' => $this->cacheDir]);
        $this->cache->setDriver()->useFileDriver();
    }

    protected function tearDown(): void
    {
        $this->cache->flushCache();
    }

    public function testCompressionFeature()
    {
        $this->cache->useCompression();
        $data = ['foo' => 'bar'];

        $this->cache->putCache('compression_key', $data);
        $this->assertTrue($this->cache->isSuccess());

        $cached = $this->cache->getCache('compression_key');
        $this->assertEquals($data, $cached);
    }

    public function testEncryptionFeature()
    {
        $this->cache->useEncryption('secret');
        $data = ['foo' => 'bar'];

        $this->cache->putCache('encryption_key', $data);
        $this->assertTrue($this->cache->isSuccess());

        $cached = $this->cache->getCache('encryption_key');

        $this->assertEquals($data, $cached);
    }

    public function testCompressionAndEncryptionTogether()
    {
        $this->cache->useCompression();
        $this->cache->useEncryption('secret');
        $data = ['foo' => 'bar'];

        $this->cache->putCache('secure_key', $data);
        $this->assertTrue($this->cache->isSuccess());

        $cached = $this->cache->getCache('secure_key');
        $this->assertEquals($data, $cached);
    }
}

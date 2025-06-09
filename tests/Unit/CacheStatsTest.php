<?php

use PHPUnit\Framework\TestCase;
use Silviooosilva\CacheerPhp\Cacheer;

class CacheStatsTest extends TestCase
{
    private $cache;
    private $cacheDir;

    protected function setUp(): void
    {
        $this->cacheDir = __DIR__ . '/cache_stats';
        if (!file_exists($this->cacheDir) || !is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }

        $this->cache = new Cacheer(['cacheDir' => $this->cacheDir]);
    }

    protected function tearDown(): void
    {
        $this->cache->flushCache();
    }

    public function test_stats_record_hits_and_misses()
    {
        $this->cache->getCache('missing_key');
        $this->cache->putCache('key', 'data');
        $this->cache->getCache('key');

        $stats = $this->cache->getStats();
        $this->assertEquals(1, $stats->getHitCount());
        $this->assertEquals(1, $stats->getMissCount());
        $this->assertGreaterThan(0, $stats->getAverageReadTime());
        $this->assertGreaterThan(0, $stats->getAverageWriteTime());
    }
}

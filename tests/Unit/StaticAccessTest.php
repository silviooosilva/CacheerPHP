<?php

use PHPUnit\Framework\TestCase;
use Silviooosilva\CacheerPhp\Cacheer;

final class StaticAccessTest extends TestCase
{
    public function testFlushCacheStatic(): void
    {
        $result = Cacheer::flushCache();
        $this->assertIsBool($result);
    }

    public function testFlushCacheDynamic(): void
    {
        $cache = new Cacheer();
        $this->assertIsBool($cache->flushCache());
    }
}

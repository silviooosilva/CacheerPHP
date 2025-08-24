<?php

use PHPUnit\Framework\TestCase;
use Silviooosilva\CacheerPhp\Cacheer;

class BooleanReturnTest extends TestCase
{
    private Cacheer $cache;

    protected function setUp(): void
    {
        $this->cache = new Cacheer();
        $this->cache->setDriver()->useArrayDriver();
    }

    public function testHasReturnsBoolean()
    {
        $this->cache->putCache('bool_key', 'value');
        $this->assertTrue($this->cache->has('bool_key'));
        $this->assertTrue($this->cache->isSuccess());

        $this->assertFalse($this->cache->has('unknown_key'));
        $this->assertFalse($this->cache->isSuccess());
    }

    public function testMutatingMethodsReturnBoolean()
    {
        $this->assertTrue($this->cache->putCache('k', 'v'));
        $this->assertTrue($this->cache->flushCache());
        $this->assertTrue($this->cache->putCache('k', 'v'));
        $this->assertTrue($this->cache->clearCache('k'));
    }
}

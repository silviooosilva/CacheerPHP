<?php

use PHPUnit\Framework\TestCase;
use Silviooosilva\CacheerPhp\Cacheer;
use Silviooosilva\CacheerPhp\Config\Option\Builder\OptionBuilder;

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

    public function testSetUp(): void
    {
        $cache = new Cacheer();
        $options = [
            'driver' => 'file',
            'path' => '/tmp/cache',
        ];
        $cache->setUp($options);
        $this->assertSame($options, $cache->options);
    }

    public static function testSetUpStatic(): void
    {
        $options = [
            'driver' => 'file',
            'path' => '/tmp/cache',
        ];
       Cacheer::setUp($options);
       self::assertSame($options, Cacheer::getOptions());
    }

    public function testSetUpStaticWithOptionBuilder(): void
    {
        $options = OptionBuilder::forFile()
            ->dir('/tmp/cache')
            ->flushAfter()->hour(2)
            ->build();

        Cacheer::setUp($options);
        self::assertSame($options, Cacheer::getOptions());
    }
}
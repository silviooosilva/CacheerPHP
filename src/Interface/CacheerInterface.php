<?php

namespace Silviooosilva\CacheerPhp\Interface;

/**
 * Class CacheerInterface
 * @author Sílvio Silva <https://github.com/silviooosilva>
 * @package Silviooosilva\CacheerPhp
 */
interface CacheerInterface
{
    public function getCache(string $cacheKey, string $namespace = '', string|int $ttl = 3600);
    public function putCache(string $cacheKey, mixed $cacheData, string $namespace = '', int|string $ttl = 3600);
    public function flushCache();
    public function clearCache(string $cacheKey, string $namespace = '');
    public function has(string $cacheKey, string $namespace = '');
    public function renewCache(string $cacheKey, int | string $ttl, string $namespace = '');
    public function appendCache(string $cacheKey, mixed $cacheData, string $namespace = '');
    public function putMany(array $items, string $namespace = '', int $batchSize = 100);
}


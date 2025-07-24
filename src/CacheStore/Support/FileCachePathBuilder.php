<?php

namespace Silviooosilva\CacheerPhp\CacheStore\Support;

use Silviooosilva\CacheerPhp\CacheStore\CacheManager\FileCacheManager;

class FileCachePathBuilder
{
    public function __construct(private FileCacheManager $fileManager, private string $baseDir)
    {
    }

    public function build(string $cacheKey, string $namespace = ''): string
    {
        $dir = $this->namespaceDir($namespace);
        if (!empty($namespace)) {
            $this->fileManager->createDirectory($dir);
        }
        return $dir . md5($cacheKey) . '.cache';
    }

    public function namespaceDir(string $namespace = ''): string
    {
        $namespace = $namespace ? md5($namespace) . '/' : '';
        $cacheDir = rtrim($this->baseDir, '/') . '/';
        return $cacheDir . $namespace;
    }
}

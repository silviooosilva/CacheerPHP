<?php

namespace Silviooosilva\CacheerPhp\CacheStore\Support;

use Silviooosilva\CacheerPhp\CacheStore\CacheManager\FileCacheManager;

/**
 * Class FileCachePathBuilder
 * @author Sílvio Silva <https://github.com/silviooosilva>
 * @package Silviooosilva\CacheerPhp
 */
class FileCachePathBuilder
{
    /**
     * FileCachePathBuilder constructor.
     *
     * @param FileCacheManager $fileManager
     * @param string $baseDir
     */
    public function __construct(private FileCacheManager $fileManager, private string $baseDir)
    {
    }

    /**
     * Builds the full path for a cache item based on its key and namespace.
     *
     * @param string $cacheKey
     * @param string $namespace
     * @return string
     */
    public function build(string $cacheKey, string $namespace = '')
    {
        $dir = $this->namespaceDir($namespace);
        if (!empty($namespace)) {
            $this->fileManager->createDirectory($dir);
        }
        return $dir . md5($cacheKey) . '.cache';
    }

    /**
     * Builds the directory path for a given namespace.
     *
     * @param string $namespace
     * @return string
     */
    public function namespaceDir(string $namespace = '')
    {
        $namespace = $namespace ? md5($namespace) . '/' : '';
        $cacheDir = rtrim($this->baseDir, '/') . '/';
        return $cacheDir . $namespace;
    }
}

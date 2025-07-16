<?php

namespace Silviooosilva\CacheerPhp\CacheStore\CacheManager;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Silviooosilva\CacheerPhp\Exceptions\CacheFileException;

/**
 * Class FileCacheManager
 * @author Sílvio Silva <https://github.com/silviooosilva>
 * @package Silviooosilva\CacheerPhp
 */
class FileCacheManager
{

    /**
    * @param string $dir
    * @return void
    */
    public function createDirectory(string $dir)
    {
        if ((!file_exists($dir) || !is_dir($dir)) && !mkdir($dir, 0755, true)) {
            throw CacheFileException::create("Could not create directory: {$dir}");
        }
    }

    /**
    * @param string $filename
    * @param string $data
    * @return void
    */
    public function writeFile(string $filename, string $data)
    {
        if (!@file_put_contents($filename, $data, LOCK_EX)) {
            throw CacheFileException::create("Could not write file: {$filename}");
        }
    }

    /**
    * @param string $filename
    * @return string
    */
    public function readFile(string $filename)
    {
        if (!$this->fileExists($filename)) {
            throw CacheFileException::create("File not found: {$filename}");
        }
        return file_get_contents($filename);
    }

    /**
    * @param string $filename
    * @return bool
    */
    public function fileExists(string $filename)
    {
        return file_exists($filename);
    }

    /**
    * @param string $filename
    * @return void
    */
    public function removeFile(string $filename)
    {
        if (file_exists($filename)) {
            unlink($filename);
        }
    }

    /**
    * @param string $dir
    * @return void
    */
    public function clearDirectory(string $dir)
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($iterator as $file) {
            $path = $file->getPathname();
            $file->isDir() ? rmdir($path) : unlink($path);
        }
    }

    /**
    * @param mixed $data
    * @param bool $serialize
    */
    public function serialize(mixed $data, bool $serialize = true)
    {
        if($serialize) {
            return serialize($data);
        }
        return unserialize($data);
    }

    /**
     * @param string $dir
     * @return array
     * @throws CacheFileException
     */
    public function getFilesInDirectory(string $dir)
    {
        if (!is_dir($dir)) {
            throw CacheFileException::create("Directory does not exist: {$dir}");
        }

        $files = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $files[] = $file->getPathname();
            }
        }

        return $files;
    }

    /**
     * @param string $dir
     * @return bool
     */
    public function directoryExists(string $dir)
    {
        return is_dir($dir);
    }
}

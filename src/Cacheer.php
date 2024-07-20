<?php

namespace Silviooosilva\CacheerPhp;

use Exception;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Class CacheerPHP
 *
 * @author Sílvio Silva <https://github.com/silviooosilva>
 * @package Silviooosilva\CacheerPhp
 */
class Cacheer
{
    /**
     * @var string
     */
    private string $cacheDir;

    /**
     * @var array
     */
    private array $options = [];

    /**
     * @var string
     */
    private string $message;

    /**
     * @var integer
     */
    private int $defaultTTL = 3600; // 1 hora por padrão

    /**
     * @var boolean
     */
    private bool $success;

    /**
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        if (!empty($options)) {
            $this->options = $options;
        }
        if (!isset($options['cacheDir'])) {
            throw new Exception("The 'cacheDir' option is required.");
        }


        $this->cacheDir = realpath($options['cacheDir']) ?: "";
        $this->createCacheDir($options['cacheDir']);
        $this->defaultTTL = (isset($options['expirationTime']) ? $this->convertExpirationToSeconds($options['expirationTime']) : $this->defaultTTL);
    }


    /**
     * @param string $cacheKey
     * @return $this | string
     */
    public function getCache(string $cacheKey, string $namespace = '')
    {
        $namespace = $namespace ? md5($namespace) . '/' : '';
        $cacheFile = "{$this->cacheDir}/{$namespace}" . md5($cacheKey) . '.cache';
        if (file_exists($cacheFile) && (filemtime($cacheFile) > (time() - $this->defaultTTL))) {
            $this->success = true;
            return unserialize(file_get_contents($cacheFile));
        }

        $this->message = "cacheFile not found, does not exists or expired";
        $this->success = false;
        return $this;
    }


    /**
     * @param string $cacheKey
     * @param mixed $cacheData
     * @return $this | string
     */
    public function putCache(string $cacheKey, mixed $cacheData, string $namespace = '')
    {
        $namespace = $namespace ? md5($namespace) . '/' : '';
        $cacheDir = "{$this->cacheDir}/";

        if (!empty($namespace)) {
            $cacheDir = "{$this->cacheDir}/{$namespace}";
            $this->createCacheDir($cacheDir);
        }

        $cacheFile = $cacheDir . md5($cacheKey) . ".cache";
        $data = serialize($cacheData);


        if (!@file_put_contents($cacheFile, $data, LOCK_EX)) {
            throw new Exception("Could not create cache file. Check your dir permissions and try again.");
        } else {
            $this->message = "Cache file created successfully";
            $this->success = true;
        }

        return $this;
    }

    /**
     * @param string $cacheKey
     * @return $this | string
     */
    public function clearCache(string $cacheKey, string $namespace = '')
    {
        $namespace = $namespace ? md5($namespace) . '/' : '';
        $cacheFile = "{$this->cacheDir}/{$namespace}" . md5($cacheKey) . ".cache";
        if (file_exists($cacheFile)) {
            unlink($cacheFile);
            $this->message = "Cache file deleted successfully!";
            $this->success = true;
        } else {
            $this->message = "Cache file does not exists!";
            $this->success = false;
        }
        return $this;
    }

    /**
     * @return $this | string
     */
    public function flushCache()
    {
        $cacheDir = "{$this->cacheDir}/";


        $cacheFiles = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($cacheDir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        if (count(scandir($cacheDir)) <= 2) {
            $this->message = "No CacheFiles in {$cacheDir}";
            $this->success = false;
        }

        foreach ($cacheFiles as $cacheFile) {
            $cachePath = $cacheFile->getPathname();
            if ($cacheFile->isDir()) {
                $this->removeCacheDir($cachePath);
                $this->message = "Flush finished successfully";
                $this->success = true;
            } else {
                unlink($cachePath);
                $this->message = "Flush finished successfully";
                $this->success = true;
            }
        }



        return $this;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return boolean
     */
    public function isSuccess()
    {
        return $this->success;
    }

    /**
     * @param string $dirName
     * @return mixed
     */
    private function createCacheDir(string $dirName)
    {
        if (!file_exists($dirName) || !is_dir($dirName)) {
            if (!mkdir($dirName, 0775, true)) {
                $this->message = "Could not create cache folder";
                return $this;
            }
        }
    }

    /**
     * Convert expiration time to seconds
     * @param string $expiration
     * @return int
     */
    private function convertExpirationToSeconds(string $expiration)
    {
        if (strpos($expiration, 'second') !== false) {
            return (int)$expiration * 1;
        }
        if (strpos($expiration, 'minute') !== false) {
            return (int)$expiration * 60;
        }
        if (strpos($expiration, 'hour') !== false) {
            return (int)$expiration * 3600;
        }
    }


    /**
     * @param string $cacheDir
     * @return bool
     */
    private function removeCacheDir(string $cacheDir)
    {
        return (rmdir($cacheDir) ? true : false);
    }
}

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
     * @var string
     */
    private string $lastFlushTimeFile;

    /**
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->validateOptions($options);
        $this->initializeCacheDir($options['cacheDir']);
        $this->defaultTTL = $this->getExpirationTime($options);
        $this->lastFlushTimeFile = "{$this->cacheDir}/last_flush_time";
        $this->handleAutoFlush($options);
    }


    /**
     * @param string $cacheKey
     * @param string $namespace
     * @param string | int $ttl
     * @return $this | string
     */
    public function getCache(string $cacheKey, string $namespace = '', string | int $ttl = null)
    {
        $ttl = isset($ttl) ? (is_string($ttl) ? $this->convertExpirationToSeconds($ttl) : $ttl) : $this->defaultTTL;

        $cacheFile = $this->buildCacheFilePath($cacheKey, $namespace);
        if ($this->isCacheValid($cacheFile, $ttl)) {
            return $this->retrieveCache($cacheFile);
        }

        $this->setMessage("cacheFile not found, does not exists or expired", false);
        return $this;
    }


    /**
     * @param string $cacheKey
     * @param mixed $cacheData
     * @return $this | string
     */
    public function putCache(string $cacheKey, mixed $cacheData, string $namespace = '')
    {
        $cacheFile = $this->buildCacheFilePath($cacheKey, $namespace);
        $this->storeCache($cacheFile, $cacheData);
        return $this;
    }

    /**
     * @param string $cacheKey
     * @param mixed $cacheData
     * @param string $namespace
     * @return void
     */
    public function appendCache(string $cacheKey, mixed $cacheData, string $namespace = '')
    {
        $currentCacheFileData = $this->getCache($cacheKey, $namespace);

        if (!$this->isSuccess()) {
            $this->setMessage("Cache file does not exists", false);
            return;
        }

        if (is_array($currentCacheFileData) && is_array($cacheData)) {
            $mergedCacheData = array_merge($currentCacheFileData, $cacheData);
        } else {
            $mergedCacheData = array_merge((array)$currentCacheFileData, (array)$cacheData);
        }


        $this->putCache($cacheKey, $mergedCacheData, $namespace);
        if ($this->isSuccess()) {
            $this->setMessage("Cache updated successfully", true);
        }
    }

    /**
     * @param string $cacheKey
     * @return $this | string
     */
    public function clearCache(string $cacheKey, string $namespace = '')
    {
        $cacheFile = $this->buildCacheFilePath($cacheKey, $namespace);
        $this->removeCacheFile($cacheFile);
        return $this;
    }

    /**
     * @return $this | string
     */
    public function flushCache()
    {
        $this->clearCacheDirectory();
        file_put_contents($this->lastFlushTimeFile, time());
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
            if (!mkdir($dirName, 0777, true)) {
                $this->message = "Could not create cache folder";
                return $this;
            }
        }
    }


    /**
     * @param array $options
     * @return void
     */
    private function validateOptions(array $options)
    {
        if (!isset($options['cacheDir'])) {
            throw new Exception("The 'cacheDir' option is required.");
        }
        $this->options = $options;
    }

    /**
     * @param string $cacheDir
     * @return void
     */
    private function initializeCacheDir(string $cacheDir)
    {
        $this->cacheDir = realpath($cacheDir) ?: "";
        $this->createCacheDir($cacheDir);
    }


    /**
     * @param string $expiration
     * @return int
     */
    private function convertExpirationToSeconds(string $expiration)
    {
        $units = [
            'second' => 1,
            'minute' => 60,
            'hour' => 3600,
            'day' => 86400,
            'week' => 604800,
            'month' => 2592000,
            'year' => 31536000,
        ];

        foreach ($units as $unit => $value) {
            if (strpos($expiration, $unit) !== false) {
                return (int)$expiration * $value;
            }
        }

        throw new Exception("Invalid expiration format");
    }


    /**
     * @param array $options
     * @return integer
     */
    private function getExpirationTime(array $options): int
    {
        return isset($options['expirationTime'])
            ? $this->convertExpirationToSeconds($options['expirationTime'])
            : $this->defaultTTL;
    }


    /**
     * @param string $flushAfter
     * @return void
     */
    private function scheduleFlush(string $flushAfter)
    {
        $flushAfterSeconds = $this->convertExpirationToSeconds($flushAfter);

        if (file_exists($this->lastFlushTimeFile)) {
            $lastFlushTime = file_get_contents($this->lastFlushTimeFile);
            if ((time() - (int)$lastFlushTime) >= $flushAfterSeconds) {
                $this->flushCache();
            }
        } else {
            file_put_contents($this->lastFlushTimeFile, time());
        }
    }

    /**
     * @param array $options
     * @return void
     */
    private function handleAutoFlush(array $options)
    {
        if (isset($options['flushAfter'])) {
            $this->scheduleFlush($options['flushAfter']);
        }
    }

    /**
     * @param string $message
     * @param boolean $success
     * @return void
     */
    private function setMessage(string $message, bool $success)
    {
        $this->message = $message;
        $this->success = $success;
    }

    /**
     * @param string $cacheDir
     * @return bool
     */
    private function removeCacheDir(string $cacheDir)
    {
        return (rmdir($cacheDir) ? true : false);
    }

    /**
     * @param string $cacheKey
     * @param string $namespace
     * @return string
     */
    private function buildCacheFilePath(string $cacheKey, string $namespace)
    {
        $namespace = $namespace ? md5($namespace) . '/' : '';
        $cacheDir = "{$this->cacheDir}/";

        if (!empty($namespace)) {
            $cacheDir = "{$this->cacheDir}/{$namespace}";
            $this->createCacheDir($cacheDir);
        }
        return $cacheDir . md5($cacheKey) . ".cache";
    }

    /**
     * @param string $cacheFile
     * @param integer $ttl
     * @return boolean
     */
    private function isCacheValid(string $cacheFile, int $ttl): bool
    {
        return file_exists($cacheFile) && (filemtime($cacheFile) > (time() - $ttl));
    }

    /**
     * @param string $cacheFile
     * @return string
     */
    private function retrieveCache(string $cacheFile)
    {
        $this->setMessage("Cache retrieved successfully", true);
        return unserialize(file_get_contents($cacheFile));
    }

    /**
     * @param string $cacheFile
     * @param mixed $cacheData
     * @return void
     */
    private function storeCache(string $cacheFile, mixed $cacheData)
    {
        $data = serialize($cacheData);

        if (!@file_put_contents($cacheFile, $data, LOCK_EX)) {
            throw new Exception("Could not create cache file. Check your dir permissions and try again.");
        }
        $this->setMessage("Cache file created successfully", true);
    }

    /**
     * @param string $cacheFile
     * @return void
     */
    private function removeCacheFile(string $cacheFile)
    {
        if (file_exists($cacheFile)) {
            unlink($cacheFile);
            $this->setMessage("Cache file deleted successfully!", true);
            return;
        }
        $this->setMessage("Cache file does not exist!", false);
    }

    /**
     * @return void
     */
    private function clearCacheDirectory()
    {
        $cacheFiles = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->cacheDir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($cacheFiles as $cacheFile) {
            $cachePath = $cacheFile->getPathname();
            $cacheFile->isDir() ? $this->removeCacheDir($cachePath) : unlink($cachePath);
        }

        $this->setMessage("Flush finished successfully", true);
    }
}

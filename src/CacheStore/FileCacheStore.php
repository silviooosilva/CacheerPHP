<?php

namespace Silviooosilva\CacheerPhp\CacheStore;

use Silviooosilva\CacheerPhp\Interface\CacheerInterface;
use Silviooosilva\CacheerPhp\CacheStore\CacheManager\FileCacheManager;
use Silviooosilva\CacheerPhp\Exceptions\CacheFileException;
use Silviooosilva\CacheerPhp\Helpers\CacheFileHelper;
use Silviooosilva\CacheerPhp\Utils\CacheLogger;

/**
 * Class FileCacheStore
 * @author Sílvio Silva <https://github.com/silviooosilva>
 * @package Silviooosilva\CacheerPhp
 */
class FileCacheStore implements CacheerInterface
{
    /**
     * @param string $cacheDir
     */
    private string $cacheDir;

    /**
     * @param array $options
     */
    private array $options = [];

    /**
     * @param string $message
     */
    private string $message = '';

    /**
     * @param integer $defaultTTL
     */
    private int $defaultTTL = 3600; // 1 hora por padrão

    /**
     * @param boolean $success
     */
    private bool $success = false;

    /**
     * @param string $lastFlushTimeFile
     */
    private string $lastFlushTimeFile;

    /**
    * @var CacheLogger
    */
    private $logger = null;

    /**
    * @var FileCacheManager
    */
    private FileCacheManager $fileManager;

    public function __construct(array $options = [])
    {
        $this->validateOptions($options);
        $this->fileManager = new FileCacheManager();
        $this->initializeCacheDir($options['cacheDir']);
        $this->defaultTTL = $this->getExpirationTime($options);
        $this->lastFlushTimeFile = "{$this->cacheDir}/last_flush_time";
        $this->handleAutoFlush($options);
        $this->logger = new CacheLogger($options['loggerPath']);
    }

    /**
     * @param string $cacheKey
     * @param mixed  $cacheData
     * @param string $namespace
     * @return void
     */
    public function appendCache(string $cacheKey, mixed $cacheData, string $namespace = '')
    {
        $currentCacheFileData = $this->getCache($cacheKey, $namespace);

        if (!$this->isSuccess()) {
            return $this->getMessage();
        }

        $mergedCacheData = CacheFileHelper::arrayIdentifier($currentCacheFileData, $cacheData);


        $this->putCache($cacheKey, $mergedCacheData, $namespace);
        if ($this->isSuccess()) {
            $this->setMessage("Cache updated successfully", true);
            $this->logger->debug("{$this->getMessage()} from file driver.");
        }
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
            $this->fileManager->createDirectory($cacheDir);
        }
        return $cacheDir . md5($cacheKey) . ".cache";
    }

    /**
     * @param string $cacheKey
     * @param string $namespace
     * @return void
     */
    public function clearCache(string $cacheKey, string $namespace = '')
    {
        $cacheFile = $this->buildCacheFilePath($cacheKey, $namespace);
        if ($this->fileManager->readFile($cacheFile)) {
            $this->fileManager->removeFile($cacheFile);
            $this->setMessage("Cache file deleted successfully!", true);
        } else {
            $this->setMessage("Cache file does not exist!", false);
        }
        $this->logger->debug("{$this->getMessage()} from file driver.");
    }

    /**
     * @return void
     */
    public function flushCache()
    {
        $this->fileManager->clearDirectory($this->cacheDir);
        file_put_contents($this->lastFlushTimeFile, time());
    }

    /**
     * @param array $options
     * @return integer
     */
    private function getExpirationTime(array $options)
    {
        return isset($options['expirationTime'])
            ? CacheFileHelper::convertExpirationToSeconds($options['expirationTime'])
            : $this->defaultTTL;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $cacheKey
     * @param string $namespace
     * @param string|int $ttl
     * @return string
     */
    public function getCache(string $cacheKey, string $namespace = '', string|int $ttl = 3600)
    {
       
        $ttl = CacheFileHelper::ttl($ttl, $this->defaultTTL);
        $cacheFile = $this->buildCacheFilePath($cacheKey, $namespace);
        if ($this->isCacheValid($cacheFile, $ttl)) {
            $cacheData = $this->fileManager->serialize($this->fileManager->readFile($cacheFile), false);

            $this->setMessage("Cache retrieved successfully", true);
            $this->logger->debug("{$this->getMessage()} from file driver.");
            return $cacheData;
        }

        $this->setMessage("cacheFile not found, does not exists or expired", false);
        $this->logger->info("{$this->getMessage()} from file driver.");
    }

    /**
     * @param array   $items
     * @param string  $namespace
     * @param integer $batchSize
     * @return void
     */
    public function putMany(array $items, string $namespace = '', int $batchSize = 100)
    {
        $processedCount = 0;
        $itemCount = count($items);

        while ($processedCount < $itemCount) {
            $batchItems = array_slice($items, $processedCount, $batchSize);
            $this->processBatchItems($batchItems, $namespace);
            $processedCount += count($batchItems);
        }
    }

    /**
     * @param string $cacheKey
     * @param mixed  $cacheData
     * @param string $namespace
     * @param string|int $ttl
     * @return void
     */
    public function putCache(string $cacheKey, mixed $cacheData, string $namespace = '', string|int $ttl = 3600)
    {
        $cacheFile = $this->buildCacheFilePath($cacheKey, $namespace);
        $data = $this->fileManager->serialize($cacheData);

        $this->fileManager->writeFile($cacheFile, $data);
        $this->setMessage("Cache file created successfully", true);

    $this->logger->debug("{$this->getMessage()} from file driver.");
}

    /**
     * @param string $cacheKey
     * @param string $namespace
     * @return void
     */
    public function has(string $cacheKey, string $namespace = '')
    {
        $this->getCache($cacheKey, $namespace);

        if ($this->isSuccess()) {
            $this->setMessage("Cache key: {$cacheKey} exists and it's available! from file driver", true);
        } else {
            $this->setMessage("Cache key: {$cacheKey} does not exists or it's expired! from file driver", false);
        }
    }

    /**
     * @param string $cacheKey
     * @param string|int $ttl
     * @param string $namespace
     * @return void
     */
    public function renewCache(string $cacheKey, string|int $ttl, string $namespace = '')
    {
        $cacheData = $this->getCache($cacheKey, $namespace);
        if ($cacheData) {
            $this->putCache($cacheKey, $cacheData, $namespace, $ttl);
            $this->setMessage("Cache with key {$cacheKey} renewed successfully", true);
            $this->logger->debug("{$this->getMessage()} from file driver.");
            return;
        }
        $this->setMessage("Failed to renew Cache with key {$cacheKey}", false);
        $this->logger->debug("{$this->getMessage()} from file driver.");
    }

    /**
     * @param array  $batchItems
     * @param string $namespace
     * @return void
     */
    private function processBatchItems(array $batchItems, string $namespace)
    {
        foreach ($batchItems as $item) {
            CacheFileHelper::validateCacheItem($item);
            $cacheKey = $item['cacheKey'];
            $cacheData = $item['cacheData'];
            $mergedData = CacheFileHelper::mergeCacheData($cacheData);
            $this->putCache($cacheKey, $mergedData, $namespace);
        }
    }

    /**
     * @return boolean
     */
    public function isSuccess()
    {
        return $this->success;
    }

    /**
     * @param string  $message
     * @param boolean $success
     * @return void
     */
    private function setMessage(string $message, bool $success)
    {
        $this->message = $message;
        $this->success = $success;
    }

    /**
     * @param array $options
     * @return void
     */
    private function validateOptions(array $options)
    {
        if (!isset($options['cacheDir']) && $options['drive'] === 'file') {
            $this->logger->debug("The 'cacheDir' option is required from file driver.");
            throw CacheFileException::create("The 'cacheDir' option is required.");
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
        $this->fileManager->createDirectory($cacheDir);
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
     * @param string $flushAfter
     * @return void
     */
    private function scheduleFlush(string $flushAfter)
    {
        $flushAfterSeconds = CacheFileHelper::convertExpirationToSeconds($flushAfter);

        if(!$this->fileManager->fileExists($this->lastFlushTimeFile)) {
            $this->fileManager->writeFile($this->lastFlushTimeFile, time());
            return;
        }

        $lastFlushTime = (int) $this->fileManager->readFile($this->lastFlushTimeFile);

        if ((time() - $lastFlushTime) >= $flushAfterSeconds) {
            $this->flushCache();
            $this->fileManager->writeFile($this->lastFlushTimeFile, time());
        }
    }

    /**
     * @param string  $cacheFile
     * @param integer $ttl
     * @return boolean
     */
    private function isCacheValid(string $cacheFile, int $ttl)
    {
        return file_exists($cacheFile) && (filemtime($cacheFile) > (time() - $ttl));
    }
}
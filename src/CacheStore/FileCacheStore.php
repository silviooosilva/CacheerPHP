<?php

namespace Silviooosilva\CacheerPhp\CacheStore;

use Silviooosilva\CacheerPhp\Interface\CacheerInterface;
use Silviooosilva\CacheerPhp\CacheStore\CacheManager\FileCacheManager;
use Silviooosilva\CacheerPhp\CacheStore\CacheManager\FileCacheFlusher;
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
    private int $defaultTTL = 3600; // 1 hour default TTL

    /**
     * @param boolean $success
     */
    private bool $success = false;


    /**
    * @var CacheLogger
    */
    private $logger = null;

    /**
    * @var FileCacheManager
    */
    private FileCacheManager $fileManager;

    /**
    * @var FileCacheFlusher
    */
    private FileCacheFlusher $flusher;


    /**
     * FileCacheStore constructor.
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->validateOptions($options);
        $this->fileManager = new FileCacheManager();
        $this->initializeCacheDir($options['cacheDir']);
        $this->flusher = new FileCacheFlusher($this->fileManager, $this->cacheDir);
        $this->defaultTTL = $this->getExpirationTime($options);
        $this->flusher->handleAutoFlush($options);
        $this->logger = new CacheLogger($options['loggerPath']);
    }

    /**
     * Appends data to an existing cache item.
     * 
     * @param string $cacheKey
     * @param mixed  $cacheData
     * @param string $namespace
     * @return mixed
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
     * Builds the cache file path based on the cache key and namespace.
     * 
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
     * Clears a specific cache item.
     * 
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
     * Flushes all cache items.
     * 
     * @return void
     */
    public function flushCache()
    {
        $this->flusher->flushCache();
    }

    /**
     * Retrieves the expiration time from options or uses the default TTL.
     * 
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
     * Retrieves a message indicating the status of the last operation.
     * 
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Retrieves a single cache item.
     * 
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
     * @param string $namespace
     * @return mixed
     */
    public function getAll(string $namespace = '')
    {
        $cacheDir = $this->getNamespaceCacheDir($namespace);

        if (!$this->fileManager->directoryExists($cacheDir)) {
            $this->setMessage("Cache directory does not exist", false);
            $this->logger->info("{$this->getMessage()} from file driver.");
            return [];
        }

        $results = $this->getAllCacheFiles($cacheDir);

        if (!empty($results)) {
            $this->setMessage("Cache retrieved successfully", true);
            $this->logger->debug("{$this->getMessage()} from file driver.");
            return $results;
        }

        $this->setMessage("No cache data found for the provided namespace", false);
        $this->logger->info("{$this->getMessage()} from file driver.");
        return [];
    }

    /**
     * Return the cache directory for the given namespace.
     * 
     * @param string $namespace
     * @return string
     */
    private function getNamespaceCacheDir(string $namespace)
    {
        $namespace = $namespace ? md5($namespace) . '/' : '';
        return "{$this->cacheDir}/{$namespace}";
    }

    /**
     * Return all valid cache files from the specified directory.
     * 
     * @param string $cacheDir
     * @return array
     */
    private function getAllCacheFiles(string $cacheDir)
    {
        $files = $this->fileManager->getFilesInDirectory($cacheDir);
        $results = [];

        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'cache') {
                $cacheKey = basename($file, '.cache');
                $cacheData = $this->fileManager->serialize($this->fileManager->readFile($file), false);
                $results[$cacheKey] = $cacheData;
            }
        }
        return $results;
    }

    /**
     * Gets the cache data for multiple keys.
     * 
     * @param array  $cacheKeys
     * @param string $namespace
     * @param string|int $ttl
     * @return mixed
     */
    public function getMany(array $cacheKeys, string $namespace = '', string|int $ttl = 3600)
    {
        $ttl = CacheFileHelper::ttl($ttl, $this->defaultTTL);
        $results = [];

        foreach ($cacheKeys as $cacheKey) {
            $cacheData = $this->getCache($cacheKey, $namespace, $ttl);
            if ($this->isSuccess()) {
                $results[$cacheKey] = $cacheData;
            } else {
                $results[$cacheKey] = null;
            }
        }

        return $results;
    }

    /**
     * Stores multiple cache items in batches.
     * 
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
     * Stores an item in the cache with a specific TTL.
     * 
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
     * Checks if a cache key exists.
     * 
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
     * Renews the cache for a specific key.
     * 
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
     * Processes a batch of cache items.
     * 
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
     * Checks if the last operation was successful.
     * 
     * @return boolean
     */
    public function isSuccess()
    {
        return $this->success;
    }

    /**
     * Sets a message indicating the status of the last operation.
     * 
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
     * Validates the options provided to the cache store.
     * 
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
     * Initializes the cache directory.
     * 
     * @param string $cacheDir
     * @return void
     */
    private function initializeCacheDir(string $cacheDir)
    {
        $this->cacheDir = realpath($cacheDir) ?: "";
        $this->fileManager->createDirectory($cacheDir);
    }



    /**
     * Checks if the cache file is valid based on its existence and modification time.
     * 
     * @param string  $cacheFile
     * @param integer $ttl
     * @return boolean
     */
    private function isCacheValid(string $cacheFile, int $ttl)
    {
        return file_exists($cacheFile) && (filemtime($cacheFile) > (time() - $ttl));
    }
}
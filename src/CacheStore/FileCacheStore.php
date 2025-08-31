<?php

namespace Silviooosilva\CacheerPhp\CacheStore;

use Silviooosilva\CacheerPhp\Interface\CacheerInterface;
use Silviooosilva\CacheerPhp\CacheStore\CacheManager\FileCacheManager;
use Silviooosilva\CacheerPhp\CacheStore\CacheManager\FileCacheFlusher;
use Silviooosilva\CacheerPhp\Exceptions\CacheFileException;
use Silviooosilva\CacheerPhp\Helpers\CacheFileHelper;
use Silviooosilva\CacheerPhp\Utils\CacheLogger;
use Silviooosilva\CacheerPhp\CacheStore\Support\FileCachePathBuilder;
use Silviooosilva\CacheerPhp\CacheStore\Support\FileCacheBatchProcessor;

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
     * @var FileCachePathBuilder
     */
    private FileCachePathBuilder $pathBuilder;
    
    /**
     * @var FileCacheBatchProcessor
     */
    private FileCacheBatchProcessor $batchProcessor;
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
     * @throws CacheFileException
     */
    public function __construct(array $options = [])
    {
        $this->validateOptions($options);
        $this->fileManager = new FileCacheManager();
        $this->initializeCacheDir($options['cacheDir']);
        $this->pathBuilder = new FileCachePathBuilder($this->fileManager, $this->cacheDir);
        $this->batchProcessor = new FileCacheBatchProcessor($this);
        $this->flusher = new FileCacheFlusher($this->fileManager, $this->cacheDir);
        $this->defaultTTL = $this->getExpirationTime($options);
        $this->flusher->handleAutoFlush($options);
        $this->logger = new CacheLogger($options['loggerPath']);
    }

    /**
     * Returns the path for a tag index file.
     * @param string $tag
     * @return string
     */
    private function getTagIndexPath(string $tag): string
    {
        $tagDir = rtrim($this->cacheDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '_tags';
        if (!$this->fileManager->directoryExists($tagDir)) {
            $this->fileManager->createDirectory($tagDir);
        }
        return $tagDir . DIRECTORY_SEPARATOR . $tag . '.json';
    }

    /**
     * Appends data to an existing cache item.
     *
     * @param string $cacheKey
     * @param mixed $cacheData
     * @param string $namespace
     * @return string|void
     * @throws CacheFileException
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
    private function buildCacheFilePath(string $cacheKey, string $namespace): string
    {
        return $this->pathBuilder->build($cacheKey, $namespace);
    }

    /**
     * Clears a specific cache item.
     *
     * @param string $cacheKey
     * @param string $namespace
     * @return bool
     * @throws CacheFileException
     */
    public function clearCache(string $cacheKey, string $namespace = ''): void
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
    public function flushCache(): void
    {
        $this->flusher->flushCache();
    }

    /**
     * Associates one or more keys to a tag.
     *
     * @param string $tag
     * @param string ...$keys
     * @return bool
     */
    public function tag(string $tag, string ...$keys): bool
    {
        $path = $this->getTagIndexPath($tag);
        $current = [];
        if ($this->fileManager->fileExists($path)) {
            $json = $this->fileManager->readFile($path);
            $decoded = json_decode($json, true);
            if (is_array($decoded)) {
                $current = $decoded;
            }
        }
        foreach ($keys as $key) {
            // Store either raw key or "namespace:key"
            $current[$key] = true;
        }
        $this->fileManager->writeFile($path, json_encode($current));
        $this->setMessage("Tagged successfully", true);
        $this->logger->debug("{$this->getMessage()} from file driver.");
        return true;
    }

    /**
     * Flush all keys associated with a tag.
     *
     * @param string $tag
     * @return void
     */
    public function flushTag(string $tag): void
    {
        $path = $this->getTagIndexPath($tag);
        $current = [];
        if ($this->fileManager->fileExists($path)) {
            $json = $this->fileManager->readFile($path);
            $current = json_decode($json, true) ?: [];
        }
        foreach (array_keys($current) as $key) {
            if (str_contains($key, ':')) {
                [$np, $k] = explode(':', $key, 2);
                $this->clearCache($k, $np);
            } else {
                $this->clearCache($key, '');
            }
        }
        if ($this->fileManager->fileExists($path)) {
            $this->fileManager->removeFile($path);
        }
        $this->setMessage("Tag flushed successfully", true);
        $this->logger->debug("{$this->getMessage()} from file driver.");
    }

    /**
     * Retrieves the expiration time from options or uses the default TTL.
     *
     * @param array $options
     * @return integer
     * @throws CacheFileException
     */
    private function getExpirationTime(array $options): int
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
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Retrieves a single cache item.
     *
     * @param string $cacheKey
     * @param string $namespace
     * @param string|int $ttl
     * @return mixed
     * @throws CacheFileException return string|void
     */
    public function getCache(string $cacheKey, string $namespace = '', string|int $ttl = 3600): mixed
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
        return null;
    }

    /**
     * @param string $namespace
     * @return array
     * @throws CacheFileException
     */
    public function getAll(string $namespace = ''): array
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
    private function getNamespaceCacheDir(string $namespace): string
    {
        return $this->pathBuilder->namespaceDir($namespace);
    }

    /**
     * Return all valid cache files from the specified directory.
     *
     * @param string $cacheDir
     * @return array
     * @throws CacheFileException
     */
    private function getAllCacheFiles(string $cacheDir): array
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
     * @param array $cacheKeys
     * @param string $namespace
     * @param string|int $ttl
     * @return array
     * @throws CacheFileException
     */
    public function getMany(array $cacheKeys, string $namespace = '', string|int $ttl = 3600): array
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
    public function putMany(array $items, string $namespace = '', int $batchSize = 100): void
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
     * @param mixed $cacheData
     * @param string $namespace
     * @param string|int $ttl
     * @return void
     * @throws CacheFileException
     */
    public function putCache(string $cacheKey, mixed $cacheData, string $namespace = '', string|int $ttl = 3600): void
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
     * @return bool
     * @throws CacheFileException
     */
    public function has(string $cacheKey, string $namespace = ''): bool
    {
        $this->getCache($cacheKey, $namespace);

        if ($this->isSuccess()) {
            $this->setMessage("Cache key: {$cacheKey} exists and it's available! from file driver", true);
            return true;
        }

        $this->setMessage("Cache key: {$cacheKey} does not exists or it's expired! from file driver", false);
        return false;
    }

    /**
     * Renews the cache for a specific key.
     *
     * @param string $cacheKey
     * @param string|int $ttl
     * @param string $namespace
     * @return void
     * @throws CacheFileException
     */
    public function renewCache(string $cacheKey, string|int $ttl, string $namespace = ''): void
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
    private function processBatchItems(array $batchItems, string $namespace): void
    {
        $this->batchProcessor->process($batchItems, $namespace);
    }

    /**
     * Checks if the last operation was successful.
     * 
     * @return boolean
     */
    public function isSuccess(): bool
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
    private function setMessage(string $message, bool $success): void
    {
        $this->message = $message;
        $this->success = $success;
    }

    /**
     * Validates the options provided to the cache store.
     *
     * @param array $options
     * @return void
     * @throws CacheFileException
     */
    private function validateOptions(array $options): void
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
     * @throws CacheFileException
     */
    private function initializeCacheDir(string $cacheDir): void
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
    private function isCacheValid(string $cacheFile, int $ttl): bool
    {
        return file_exists($cacheFile) && (filemtime($cacheFile) > (time() - $ttl));
    }
}

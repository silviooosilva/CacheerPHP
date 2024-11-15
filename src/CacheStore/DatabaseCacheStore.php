<?php

namespace Silviooosilva\CacheerPhp\CacheStore;

use Exception;
use Silviooosilva\CacheerPhp\Utils\CacheLogger;
use Silviooosilva\CacheerPhp\Repositories\CacheDatabaseRepository;

/**
 * Class DatabaseCacheStore
 * @author Sílvio Silva <https://github.com/silviooosilva>
 * @package Silviooosilva\CacheerPhp
 */
class DatabaseCacheStore
{
    /**
     * @var boolean
     */
    private bool $success;

    /**
     * @var string
     */
    private string $message;

    /**
     * Summary of logger
     */
    private $logger = null;

    /**
     * Summary of cacheRepository
     */
    private $cacheRepository;

    public function __construct(string $logPath = null)
    {
        $this->logger = new CacheLogger($logPath);
        $this->cacheRepository = new CacheDatabaseRepository();
    }


    /**
     * @param string $cacheKey
     * @param string $namespace
     * @return mixed
     */
    public function getCache(string $cacheKey, string $namespace = '')
    {
        $cacheData = $this->retrieveCache($cacheKey, $namespace);
        if ($cacheData) {
            $this->setMessage("Cache retrieved successfully", true);
            $this->logger->debug("{$this->getMessage()} from database driver.");
            return $cacheData;
        }
        $this->setMessage("CacheData not found, does not exists or expired", false);
        $this->logger->info("{$this->getMessage()} from database driver.");
        return null;
    }
    /**
     * @param string $cacheKey
     * @param mixed $cacheData
     * @param string $namespace
     * @param integer $ttl
     * @return $this
     */
    public function putCache(string $cacheKey, mixed $cacheData, string $namespace = '', int | string $ttl = 3600)
    {
        $this->storeCache($cacheKey, $cacheData, $namespace, $ttl);
        $this->logger->debug("{$this->getMessage()} from database driver.");
        return $this;
    }

    /**
     * @param array $items
     * @param string $namespace
     * @param integer $batchSize
     * @return void
     */
    public function putMany(array $items, string $namespace = '', int $batchSize = 100)
    {
        $processedCount = 0;
        $itemCount = count($items);

        while ($processedCount < $itemCount) {
            $batchItems = array_slice($items, $processedCount, $batchSize);
            foreach ($batchItems as $item) {
                if (isset($item['cacheKey']) && isset($item['cacheData'])) {
                    $cacheKey = $item['cacheKey'];
                    $cacheData = $item['cacheData'];


                    if (is_array($cacheData) && is_array(reset($cacheData))) {
                        $mergedData = [];
                        foreach ($cacheData as $data) {
                            $mergedData[] = $data;
                        }
                    } else {
                        $mergedData = $cacheData;
                    }

                    $this->putCache($cacheKey, $mergedData, $namespace);
                } else {
                    $this->logger->info("Each item must contain 'cacheKey' and 'cacheData' from database driver.");
                    throw new Exception("Each item must contain 'cacheKey' and 'cacheData'");
                }
            }

            $processedCount += count($batchItems);
        }
    }

    /**
     * @param string $cacheKey
     * @param mixed $cacheData
     * @param string $namespace
     * @return void | string
     */
    public function appendCache(string $cacheKey, mixed $cacheData, string $namespace = '')
    {
        $currentCacheData = $this->getCache($cacheKey, $namespace);

        if (is_array($currentCacheData) && is_array($cacheData)) {
            $mergedCacheData = array_merge($currentCacheData, $cacheData);
        } else {
            $mergedCacheData = array_merge((array)$currentCacheData, (array)$cacheData);
        }

        if ($this->updateCache($cacheKey, $mergedCacheData, $namespace)) {
            $this->setMessage("Cache updated successfully", true);
            $this->logger->debug("{$this->getMessage()} from database driver.");
        }
    }

    /**
     * @param string $cacheKey
     * @param string $namespace
     * @return void
     */
    public function clearCache(string $cacheKey, string $namespace = '')
    {
        $data = $this->cacheRepository->clear($cacheKey, $namespace);
        return $this->setMessage($data ? "Cache deleted successfully!" : "Cache does not exist!", $data);
    }

    /**
     * @return void
     */
    public function flushCache()
    {
        $this->cacheRepository->flush();
        $this->setMessage("Flush finished successfully", true);
        $this->logger->info("{$this->getMessage()} from database driver.");
    }


    /**
     * @param string $cacheKey
     * @param string $namespace
     * @return mixed
     */
    private function retrieveCache(string $cacheKey, string $namespace = '')
    {
        return $this->cacheRepository->retrieve($cacheKey, $namespace);
    }

    /**
     * @param string $cacheKey
     * @param mixed $cacheData
     * @param string $namespace
     * @param integer $ttl
     * @return void
     */
    private function storeCache(string $cacheKey, mixed $cacheData, string $namespace = '', int | string $ttl = 3600)
    {
        $data = $this->cacheRepository->store($cacheKey, $cacheData, $namespace, $ttl);
        return $data ? $this->setMessage("Cache Stored Successfully", true) :
            $this->setMessage("Already exists a cache with this key...", false);
    }


    /**
     * @param string $cacheKey
     * @param mixed $cacheData
     * @param string $namespace
     * @return bool
     */
    private function updateCache(string $cacheKey, mixed $cacheData, string $namespace = '')
    {
        $data = $this->cacheRepository->update($cacheKey, $cacheData, $namespace);
        $this->setMessage(
            $data ? "Cache updated successfully!" : "Cache does not exist or update failed!",
            $data
        );
        return $data;
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
}

<?php

namespace Silviooosilva\CacheerPhp\CacheStore\CacheManager;

use Silviooosilva\CacheerPhp\Helpers\CacheFileHelper;

/**
 * Class FileCacheFlusher
 * Manages flushing and auto-flush scheduling for file cache.
 */
class FileCacheFlusher
{
    private FileCacheManager $fileManager;
    private string $cacheDir;
    private string $lastFlushTimeFile;

    public function __construct(FileCacheManager $fileManager, string $cacheDir)
    {
        $this->fileManager = $fileManager;
        $this->cacheDir = $cacheDir;
        $this->lastFlushTimeFile = "$cacheDir/last_flush_time";
    }

    /**
     * Flushes all cache items and updates the last flush timestamp.
     */
    public function flushCache(): void
    {
        $this->fileManager->clearDirectory($this->cacheDir);
        file_put_contents($this->lastFlushTimeFile, time());
    }

    /**
     * Handles the auto-flush functionality based on options.
     */
    public function handleAutoFlush(array $options): void
    {
        if (isset($options['flushAfter'])) {
            $this->scheduleFlush($options['flushAfter']);
        }
    }

    /**
     * Schedules a flush operation based on the provided interval.
     */
    private function scheduleFlush(string $flushAfter): void
    {
        $flushAfterSeconds = CacheFileHelper::convertExpirationToSeconds($flushAfter);

        if (!$this->fileManager->fileExists($this->lastFlushTimeFile)) {
            $this->fileManager->writeFile($this->lastFlushTimeFile, time());
            return;
        }

        $lastFlushTime = (int) $this->fileManager->readFile($this->lastFlushTimeFile);

        if ((time() - $lastFlushTime) >= $flushAfterSeconds) {
            $this->flushCache();
            $this->fileManager->writeFile($this->lastFlushTimeFile, time());
        }
    }
}

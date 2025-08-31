<?php

namespace Silviooosilva\CacheerPhp\CacheStore\CacheManager;

use Silviooosilva\CacheerPhp\Helpers\CacheFileHelper;

/**
 * Class GenericFlusher
 * Lightweight flusher that stores last flush timestamp in a file and invokes a provided callback.
 */
class GenericFlusher
{
    /** @var string */
    private string $lastFlushTimeFile;

    /** @var callable */
    private $flushCallback;

    /**
     * @param string   $lastFlushTimeFile
     * @param callable $flushCallback  Callback that performs the actual flush
     */
    public function __construct(string $lastFlushTimeFile, callable $flushCallback)
    {
        $this->lastFlushTimeFile = $lastFlushTimeFile;
        $this->flushCallback = $flushCallback;
    }

    /**
     * Performs a flush and records the time.
     * @return void
     */
    public function flushCache(): void
    {
        ($this->flushCallback)();
        @file_put_contents($this->lastFlushTimeFile, (string) time());
    }

    /**
     * Handles auto-flush if 'flushAfter' is present in options.
     * @param array $options
     * @return void
     */
    public function handleAutoFlush(array $options): void
    {
        if (!isset($options['flushAfter'])) {
            return;
        }
        $this->scheduleFlush((string) $options['flushAfter']);
    }

    /**
     * @param string $flushAfter
     * @return void
     */
    private function scheduleFlush(string $flushAfter): void
    {
        $flushAfterSeconds = (int) CacheFileHelper::convertExpirationToSeconds($flushAfter);

        if (!file_exists($this->lastFlushTimeFile)) {
            @file_put_contents($this->lastFlushTimeFile, (string) time());
            return;
        }

        $lastFlushTime = (int) @file_get_contents($this->lastFlushTimeFile);

        if ((time() - $lastFlushTime) >= $flushAfterSeconds) {
            $this->flushCache();
            @file_put_contents($this->lastFlushTimeFile, (string) time());
        }
    }
}


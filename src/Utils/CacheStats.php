<?php

namespace Silviooosilva\CacheerPhp\Utils;

class CacheStats
{
    private int $hits = 0;
    private int $misses = 0;
    private array $readTimes = [];
    private array $writeTimes = [];
    private ?CacheLogger $logger = null;

    public function __construct(CacheLogger $logger = null)
    {
        $this->logger = $logger;
    }

    public function recordHit(float $time): void
    {
        $this->hits++;
        $this->readTimes[] = $time;
        if ($this->logger) {
            $this->logger->debug("Cache hit in {$time} seconds.");
        }
    }

    public function recordMiss(float $time): void
    {
        $this->misses++;
        $this->readTimes[] = $time;
        if ($this->logger) {
            $this->logger->debug("Cache miss in {$time} seconds.");
        }
    }

    public function recordWrite(float $time): void
    {
        $this->writeTimes[] = $time;
        if ($this->logger) {
            $this->logger->debug("Cache write in {$time} seconds.");
        }
    }

    public function getHitCount(): int
    {
        return $this->hits;
    }

    public function getMissCount(): int
    {
        return $this->misses;
    }

    public function getAverageReadTime(): float
    {
        if (empty($this->readTimes)) {
            return 0;
        }
        return array_sum($this->readTimes) / count($this->readTimes);
    }

    public function getAverageWriteTime(): float
    {
        if (empty($this->writeTimes)) {
            return 0;
        }
        return array_sum($this->writeTimes) / count($this->writeTimes);
    }
}

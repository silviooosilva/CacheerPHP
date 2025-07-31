<?php

use PHPUnit\Framework\TestCase;
use Silviooosilva\CacheerPhp\Cacheer;
use Silviooosilva\CacheerPhp\Core\Connect;

class CacheerPerformanceTest extends TestCase
{
    private Cacheer $cacheer;
    private const TEST_KEY = 'test_key';
    private const TEST_VALUE = 'test_value';
    private const ITERATIONS = 1000;

    protected function setUp(): void
    {
        $this->cacheer = new Cacheer();
        $this->cacheer->setConfig()->setLoggerPath('cacheer_test_performance.log'); // Adjust for your environment
    }

    /**
     * Test the performance of the file-based cache driver
     */
    public function testFileDriverPerformance()
    {
        $this->cacheer->setDriver()->useFileDriver();
        $this->runPerformanceTest('File Driver');
        $this->assertTrue(true); // Minimal assertion
    }

    /**
     * Test the performance of the database cache driver
     */
    public function testDatabaseDriverPerformance()
    {
        $this->cacheer->setConfig()->setDatabaseConnection(Connect::getInstance()->getAttribute(PDO::ATTR_DRIVER_NAME));
        $this->cacheer->setDriver()->useDatabaseDriver();
        $this->cacheer->setConfig()->setTimeZone('America/Toronto');
        $this->runPerformanceTest('Database Driver');
        $this->assertTrue(true); // Minimal assertion
    }

    /**
     * Test the performance of the Redis cache driver
     */
    public function testRedisDriverPerformance()
    {
        $this->cacheer->setDriver()->useRedisDriver();
        $this->runPerformanceTest('Redis Driver');
        $this->assertTrue(true); // Minimal assertion
    }

    /**
     * Test the performance of the array cache driver
     */
    public function testArrayDriverPerformance()
    {
        $this->cacheer->setDriver()->useArrayDriver();
        $this->runPerformanceTest('Array Driver');
        $this->assertTrue(true); // Minimal assertion
    }

    /**
     * Run performance tests for the specified driver
     */
    private function runPerformanceTest(string $driverName): void
    {
        echo "\nTesting $driverName...\n";

        // Write Test
        $startMemory = memory_get_usage(true);
        $startTime = microtime(true);

        for ($i = 0; $i < self::ITERATIONS; $i++) {
            $this->cacheer->putCache(self::TEST_KEY . $i, self::TEST_VALUE);
        }

        $writeTime = microtime(true) - $startTime;
        $writeMemory = memory_get_usage(true) - $startMemory;

        $formatedTimeWriting = round($writeTime, 2);
        $formatedTimeMemoryWriting = round($writeMemory, 2);

        echo "Write Test: {$formatedTimeWriting}s, Memory: {$formatedTimeMemoryWriting} bytes\n";

        // Read Test
        $startMemory = memory_get_usage(true);
        $startTime = microtime(true);

        for ($i = 0; $i < self::ITERATIONS; $i++) {
            $this->cacheer->getCache(self::TEST_KEY . $i);
        }

        $readTime = microtime(true) - $startTime;
        $readMemory = memory_get_usage(true) - $startMemory;


        $formatedTimeReading = round($readTime, 2);
        $formatedTimeMemoryReading = round($readMemory, 2);

        echo "Read Test: {$formatedTimeReading}s, Memory: {$formatedTimeMemoryReading} bytes\n";

        // Delete Test
        $startMemory = memory_get_usage(true);
        $startTime = microtime(true);

        for ($i = 0; $i < self::ITERATIONS; $i++) {
            $this->cacheer->clearCache(self::TEST_KEY . $i);
        }

        $deleteTime = microtime(true) - $startTime;
        $deleteMemory = memory_get_usage(true) - $startMemory;

        $formatedTimeDelete = round($deleteTime, 2);
        $formatedTimeMemoryDelete = round($deleteMemory, 2);

        echo "Delete Test: {$formatedTimeDelete}s, Memory: {$formatedTimeMemoryDelete} bytes\n";
    }
}

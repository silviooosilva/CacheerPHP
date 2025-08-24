<?php

namespace Silviooosilva\CacheerPhp;

use Closure;
use Silviooosilva\CacheerPhp\CacheStore\DatabaseCacheStore;
use Silviooosilva\CacheerPhp\CacheStore\FileCacheStore;
use Silviooosilva\CacheerPhp\CacheStore\RedisCacheStore;
use Silviooosilva\CacheerPhp\CacheStore\ArrayCacheStore;
use Silviooosilva\CacheerPhp\Helpers\CacheConfig;
use Silviooosilva\CacheerPhp\Utils\CacheDataFormatter;
use Silviooosilva\CacheerPhp\Utils\CacheDriver;
use RuntimeException;
use Silviooosilva\CacheerPhp\Service\CacheRetriever;
use Silviooosilva\CacheerPhp\Service\CacheMutator;
use BadMethodCallException;

/**
* Class CacheerPHP
* @author Sílvio Silva <https://github.com/silviooosilva>
* @package Silviooosilva\CacheerPhp
*/
final class Cacheer
{
    /**
    * @var string
    */
    private string $message;

    /**
    * @var boolean
    */
    private bool $success;

    /**
    * @var boolean
    */
    private bool $formatted = false;

    /**
    * @var bool
    */
    private bool $compression = false;

    /**
    * @var string|null
    */
    private ?string $encryptionKey = null;

    /**
    * @var FileCacheStore|DatabaseCacheStore|RedisCacheStore|ArrayCacheStore
    */
    public RedisCacheStore|DatabaseCacheStore|ArrayCacheStore|FileCacheStore $cacheStore;

    /**
    * @var array
    */
    public array $options = [];

    /**
    * @var CacheRetriever
    */
    private CacheRetriever $retriever;

    /**
    * @var CacheMutator
    */
    private CacheMutator $mutator;

    /**
    * @var Cacheer|null
    */
    private static ?Cacheer $staticInstance = null;

/**
    * Cacheer constructor.
    *
    * @param array $options
    * @param bool  $formatted
    * @throws RuntimeException|Exceptions\CacheFileException
 */
    public function __construct(array $options = [], bool $formatted = false)
    {
        $this->formatted = $formatted;
        $this->validateOptions($options);
        $this->retriever = new CacheRetriever($this);
        $this->mutator = new CacheMutator($this);
        $this->setDriver()->useDefaultDriver();
    }

    private static function instance(): Cacheer
    {
        return self::$staticInstance ??= new self();
    }

    /**
    * Checks if the last operation was successful.
    * 
    * @return bool
    */
    public function isSuccess(): bool
    {
        return $this->success;
    }

    /**
    * Returns a CacheConfig instance for configuration management.
    * 
    * @return CacheConfig
    */
    public function setConfig(): CacheConfig
    {
        return new CacheConfig($this);
    }

    /**
    * Sets the cache driver based on the configuration.
    * 
    * @return CacheDriver
    */
    public function setDriver(): CacheDriver
    {
        return new CacheDriver($this);
    }

    /**
    * Sets a message for the cache operation.
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
    * Retrieves the message from the last operation.
    * 
    * @return string
    */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return void
     */
    public function syncState(): void
    {
        $this->setMessage($this->cacheStore->getMessage(), $this->cacheStore->isSuccess());
    }

    /**
     * @param string $message
     * @param bool $success
     * @return void
     */
    public function setInternalState(string $message, bool $success): void
    {
        $this->setMessage($message, $success);
    }

    /**
     * @return bool
     */
    public function isFormatted(): bool
    {
        return $this->formatted;
    }

    /**
     * @return bool
     */
    public function isCompressionEnabled(): bool
    {
        return $this->compression;
    }

    /**
     * @return string|null
     */
    public function getEncryptionKey(): ?string
    {
        return $this->encryptionKey;
    }

    /**
    * Enables or disables the formatter for cache data.
    * 
    * @return void
    */
    public function useFormatter(): void
    {
        $this->formatted = !$this->formatted;
    }

    /**
    * Validates the options provided for the Cacheer instance.
    * 
    * @param array $options
    * @return void
    */
    private function validateOptions(array $options): void
    {
        $this->options = $options;
    }

    /**
    * Enable or disable data compression
    *
    * @param bool $status
    * @return $this
    */
    public function useCompression(bool $status = true): Cacheer
    {
        $this->compression = $status;
        return $this;
    }

    /**
    * Enable encryption for cached data
    *
    * @param string $key
    * @return $this
    */
    public function useEncryption(string $key): Cacheer
    {
        $this->encryptionKey = $key;
        return $this;
    }

    /**
    * Dynamically handle calls to missing instance methods.
    *
    * @param string $method
    * @param array $parameters
    * @return mixed
    */
    public function __call(string $method, array $parameters): mixed
    {
        if (method_exists($this->mutator, $method)) {
            return $this->mutator->{$method}(...$parameters);
        }

        if (method_exists($this->retriever, $method)) {
            return $this->retriever->{$method}(...$parameters);
        }

        throw new BadMethodCallException("Method {$method} does not exist");
    }

    /**
    * Handle dynamic static calls by routing them through an instance.
    *
    * @param string $method
    * @param array $parameters
    * @return mixed
    */
    public static function __callStatic(string $method, array $parameters): mixed
    {
        $instance = self::instance();

        return $instance->__call($method, $parameters);
    }
}

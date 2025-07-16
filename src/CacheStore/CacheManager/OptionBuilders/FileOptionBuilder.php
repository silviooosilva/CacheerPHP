<?php

declare(strict_types=1);

namespace Silviooosilva\CacheerPhp\CacheStore\CacheManager\OptionBuilders;

use Silviooosilva\CacheerPhp\Support\TimeBuilder;

/**
 * Class FileOptionBuilder
 * @author Sílvio Silva <https://github.com/silviooosilva>
 * @package Silviooosilva\CacheerPhp
 */
class FileOptionBuilder
{
  /** @param null|string $cacheDir */
  private ?string $cacheDir = null;

  /** @param null|string $expirationTime */
  private ?string $expirationTime = null;

  /** @param null|string $flushAfter */
  private ?string $flushAfter = null;

  /** @param array $options */
  private array $options = [];

  /**
  * Directory where cache files will be stored.
  *
  * @param string $cacheDir
  * @return $this
  */
  public function dir(string $cacheDir)
  {
    $this->cacheDir = $cacheDir;
    return $this;
  }

  /**
  * Sets the expiration time for cache items.
  * @param ?string $expirationTime
  * @return $this|TimeBuilder
  */
  public function expirationTime(?string $expirationTime = null)
  {

    if (!is_null($expirationTime)) {
      $this->expirationTime = $expirationTime;
      return $this;
    }

    return new TimeBuilder(function ($formattedTime){
      $this->expirationTime = $formattedTime;
    }, $this);
  }

  /**
  * Sets the flush time for cache items.
  * This is the time after which the cache will be flushed.
  *
  * @param ?string $flushAfter
  * @return $this|TimeBuilder
  */
  public function flushAfter(?string $flushAfter = null)
  {

    if (!is_null($flushAfter)) {
      $this->flushAfter = mb_strtolower($flushAfter, 'UTF-8');
      return $this;
    }

    return new TimeBuilder(function ($formattedTime){
      $this->flushAfter = $formattedTime;
    }, $this);
  }

  /**
  * Builds the options array for file cache configuration.
  * @return array
  */
  public function build()
  {
    return $this->validated();
  }

  /**
  * Validates the properties and returns an array of options.
  * It checks if each property is valid and not null, then adds it to the options
  *
  * @return array
  */
  private function validated()
  {
    foreach ($this->properties() as $key => $value) {
        if ($this->isValidAndNotNull($value)) {
            $this->options[$key] = $value;
        }
    }
    return $this->options;
  }

  /**
  * Checks if the provided data is valid and not null.
  * This is used to ensure that only valid options are included in the final configuration.
  *
  * @param mixed $data
  * @return bool
  */
  private function isValidAndNotNull(mixed $data)
  {
    return !empty($data) ? true : false;
  }

  /**
  * Returns the properties of the FileOptionBuilder instance.
  * This method is used to gather the current state of the instance's properties.
  *
  * @return array
  */
  private function properties()
  {
    $properties = [
      'cacheDir' => $this->cacheDir,
      'expirationTime' => $this->expirationTime,
      'flushAfter' => $this->flushAfter
    ];

    return $properties;
  }
}

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
  * @param string $cacheDir
  * @return $this
  */
  public function dir(string $cacheDir)
  {
    $this->cacheDir = $cacheDir;
    return $this;
  }

  /**
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
  * @return array
  */
  public function build()
  {
    return $this->validated();
  }

  /**
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
  * @param mixed $data
  * @return bool
  */
  private function isValidAndNotNull(mixed $data)
  {
    return !empty($data) ? true : false;
  }

  /**
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

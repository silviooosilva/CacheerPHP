<?php

namespace Silviooosilva\CacheerPhp\CacheStore\CacheManager\OptionBuilders;

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
  * @param string $expirationTime
  * @return $this
  */
  public function expirationTime(string $expirationTime)
  {
    $this->expirationTime = $expirationTime;
    return $this;
  }

  /**
  * @param string $flushAfter
  * @return $this
  */
  public function flushAfter(string $flushAfter)
  {
    $this->flushAfter = mb_strtolower($flushAfter, 'UTF-8');
    return $this;
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

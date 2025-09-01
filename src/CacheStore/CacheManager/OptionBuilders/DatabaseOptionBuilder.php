<?php

declare(strict_types=1);

namespace Silviooosilva\CacheerPhp\CacheStore\CacheManager\OptionBuilders;

use Silviooosilva\CacheerPhp\Support\TimeBuilder;

/**
 * Class DatabaseOptionBuilder
 * @author Sílvio Silva <https://github.com/silviooosilva>
 * @package Silviooosilva\CacheerPhp
 */
class DatabaseOptionBuilder
{
  /** @var ?string */
  private ?string $table = null;

  /** @var ?string */
  private ?string $expirationTime = null;

  /** @var ?string */
  private ?string $flushAfter = null;

  /** @var array */
  private array $options = [];

  /**
   * Sets the database table used for cache storage.
   *
   * @param string $table
   * @return $this
   */
  public function table(string $table): self
  {
    $this->table = $table;
    return $this;
  }

  /**
   * Sets the default expiration time for records.
   *
   * @param ?string $expirationTime
   * @return $this|TimeBuilder
   */
  public function expirationTime(?string $expirationTime = null)
  {
    if (!is_null($expirationTime)) {
      $this->expirationTime = $expirationTime;
      return $this;
    }

    return new TimeBuilder(function ($formattedTime) {
      $this->expirationTime = $formattedTime;
    }, $this);
  }

  /**
   * Sets an auto-flush interval for database cache.
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

    return new TimeBuilder(function ($formattedTime) {
      $this->flushAfter = $formattedTime;
    }, $this);
  }

  /**
   * Builds the options array.
   *
   * @return array
   */
  public function build(): array
  {
    return $this->validated();
  }

  /**
   * Validate and assemble options.
   * @return array
   */
  private function validated(): array
  {
    foreach ($this->properties() as $key => $value) {
      if (!empty($value)) {
        $this->options[$key] = $value;
      }
    }
    return $this->options;
  }

  /**
   * Returns current properties.
   * @return array
   */
  private function properties(): array
  {
    return [
      'table'          => $this->table,
      'expirationTime' => $this->expirationTime,
      'flushAfter'     => $this->flushAfter,
    ];
  }
}


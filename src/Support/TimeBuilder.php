<?php

namespace Silviooosilva\CacheerPhp\Support;

use Closure;
use Silviooosilva\CacheerPhp\CacheStore\CacheManager\OptionBuilders\FileOptionBuilder;

/**
 * Class TimeBuilder
 * @author Sílvio Silva <https://github.com/silviooosilva>
 * @package Silviooosilva\CacheerPhp
 */
class TimeBuilder
{
    
  /** @param Closure $callback */
  private Closure $callback;

  /** @var ?FileOptionBuilder */
  private ?FileOptionBuilder $builder = null;

  /**
  * TimeBuilder constructor.
  * @param Closure $callback
  * @param FileOptionBuilder $builder
  *
  * @return void
  */
  public function __construct(Closure $callback, FileOptionBuilder $builder)
  {
    $this->callback = $callback;
    $this->builder = $builder;
  }

    /**
     * Sets the time in seconds.
     *
     * @param int $seconds
     * @return FileOptionBuilder|null
     */
  public function second(int $seconds): ?FileOptionBuilder
  {
    return $this->setTime($seconds, "seconds");
  }

    /**
     * Sets the time in minutes.
     *
     * @param int $minutes
     * @return FileOptionBuilder|null
     */
  public function minute(int $minutes): ?FileOptionBuilder
  {
    return $this->setTime($minutes, "minutes");
  }

    /**
     * Sets the time in hours.
     *
     * @param int $hours
     * @return FileOptionBuilder|null
     */
  public function hour(int $hours): ?FileOptionBuilder
  {
    return $this->setTime($hours, "hours");
  }

    /**
     * Sets the time in days.
     *
     * @param int $days
     * @return FileOptionBuilder|null
     */
  public function day(int $days): ?FileOptionBuilder
  {
    return $this->setTime($days, "days");
  }

    /**
     * Sets the time in weeks.
     *
     * @param int $weeks
     * @return FileOptionBuilder|null
     */
  public function week(int $weeks): ?FileOptionBuilder
  {
    return $this->setTime($weeks, "weeks");
  }

    /**
     * Sets the time in months.
     *
     * @param int $months
     * @return FileOptionBuilder|null
     */
  public function month(int $months): ?FileOptionBuilder
  {
    return $this->setTime($months, "months");
  }


    /**
     * This method sets the time value and unit, and invokes the callback with the formatted string.
     *
     * @param int $value
     * @param string $unit
     * @return FileOptionBuilder|null
     */
  private function setTime(int $value, string $unit): ?FileOptionBuilder
  {
   ($this->callback)("{$value} {$unit}");
    return $this->builder;
  }

}

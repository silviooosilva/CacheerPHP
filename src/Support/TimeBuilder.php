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

  /** @param string $unit */
  private string $unit;

  /** @param int $value */
  private int $value;

  /** @param Closure $callback */
  private Closure $callback;

  /** @param FileOptionBuilder */
  private $builder = null;

  /**
  * TimeBuilder constructor.
  * @param Closure $callback
  * @param FileOptionBuilder $builder
  *
  * @return void
  */
  public function __construct(Closure $callback, $builder)
  {
    $this->callback = $callback;
    $this->builder = $builder;
  }

  /**
  * Sets the time in seconds.
  * 
  * @param int $seconds
  * @return FileOptionBuilder|mixed
  */
  public function second(int $seconds) 
  {
    return $this->setTime($seconds, "seconds");
  }

  /**
  * Sets the time in minutes.
  *
  * @param int $minutes
  * @return FileOptionBuilder|mixed
  */
  public function minute(int $minutes) 
  {
    return $this->setTime($minutes, "minutes");
  }

  /**
  * Sets the time in hours.
  * 
  * @param int $hours
  * @return FileOptionBuilder|mixed
  */
  public function hour(int $hours) 
  {
    return $this->setTime($hours, "hours");
  }

  /**
  * Sets the time in days.
  *
  * @param int $days
  * @return FileOptionBuilder|mixed
  */
  public function day(int $days) 
  {
    return $this->setTime($days, "days");
  }

  /**
  * Sets the time in weeks.
  *
  * @param int $weeks
  * @return FileOptionBuilder|mixed
  */
  public function week(int $weeks) 
  {
    return $this->setTime($weeks, "weeks");
  }

  /**
  * Sets the time in months.
  *
  * @param int $months
  * @return FileOptionBuilder|mixed
  */
  public function month(int $months) 
  {
    return $this->setTime($months, "months");
  }
  

  /** 
  * This method sets the time value and unit, and invokes the callback with the formatted string. 
  *
  * @param int $value
  * @param string $unit
  * @return FileOptionBuilder
  */
  private function setTime(int $value, string $unit) 
  {

    $this->value = $value;
    $this->unit = $unit;
   ($this->callback)("{$value} {$unit}");
    return $this->builder;
  }

}

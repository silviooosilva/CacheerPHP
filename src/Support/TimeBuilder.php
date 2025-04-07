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

  public function __construct(Closure $callback, $builder)
  {
    $this->callback = $callback;
    $this->builder = $builder;
  }

  /**
  * @param int $value
  * @return FileOptionBuilder|mixed
  */
  public function second(int $value) 
  {
    return $this->setTime($value, "seconds");
  }

  /**
  * @param int $value
  * @return FileOptionBuilder|mixed
  */
  public function minute(int $value) 
  {
    return $this->setTime($value, "minutes");
  }

  /**
  * @param int $value
  * @return FileOptionBuilder|mixed
  */
  public function hour(int $value) 
  {
    return $this->setTime($value, "hours");
  }

  /**
  * @param int $value
  * @return FileOptionBuilder|mixed
  */
  public function day(int $value) 
  {
    return $this->setTime($value, "days");
  }

  /**
  * @param int $value
  * @return FileOptionBuilder|mixed
  */
  public function week(int $value) 
  {
    return $this->setTime($value, "weeks");
  }

  /**
  * @param int $value
  * @return FileOptionBuilder|mixed
  */
  public function month(int $value) 
  {
    return $this->setTime($value, "months");
  }
  

  /**
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

<?php

namespace Silviooosilva\CacheerPhp\Config\Option\Builder;

use Silviooosilva\CacheerPhp\CacheStore\CacheManager\OptionBuilders\FileOptionBuilder;

/**
 * Class OptionBuilder
 * @author Sílvio Silva <https://github.com/silviooosilva>
 * @package Silviooosilva\CacheerPhp
 */
class OptionBuilder
{
  
  /**
  * Creates a FileOptionBuilder instance for file-based cache options.
  *
  * @return FileOptionBuilder
  */
  public static function forFile(): FileOptionBuilder
  {
    return new FileOptionBuilder();
  }
}

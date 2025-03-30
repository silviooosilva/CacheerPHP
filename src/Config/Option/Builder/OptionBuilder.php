<?php

namespace Silviooosilva\CacheerPhp\Config\Option\Builder;

use Silviooosilva\CacheerPhp\CacheStore\CacheManager\OptionBuilders\FileOptionBuilder;



class OptionBuilder
{
  /**
  * @return FileOptionBuilder
  */
  public static function forFile() {
    return new FileOptionBuilder();
  }
}

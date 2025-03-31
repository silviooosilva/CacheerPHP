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
  * @return FileOptionBuilder
  */
  public static function forFile() {
    return new FileOptionBuilder();
  }
}

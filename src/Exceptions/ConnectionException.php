<?php

namespace Silviooosilva\CacheerPhp\Exceptions;

use Exception;

class ConnectionException extends BaseException
{

  /** @param string $before */
  private static string $before = "<Connection Exception>";

  /**
  * @return void
  */
  public static function create(string $message = "", int $code = 0, ?Exception $previous = null, array $details = [])
  {
    return new self(self::getBefore() . ": " .$message, $code, $previous, $details);
  }

  /**
  * @return string
  */
  public static function getBefore()
  {
    return self::$before;
  }

  /**
  * @return void
  */
  public static function setBefore(string $text)
  {
    self::$before = $text;
  }

}

<?php

namespace Silviooosilva\CacheerPhp\Exceptions;

use Exception;

class CacheRedisException extends BaseException
{
    private static string $before = "<Redis Cache Store Exception>";

    /**
     * @param string $message
     * @param int $code
     * @param Exception|null $previous
     * @param array $details
     * @return self
     */
    public static function create(string $message = "", int $code = 0, ?Exception $previous = null, array $details = [])
    {
        return new self(self::getBefore() . ": " . $message, $code, $previous, $details);
    }

    /**
     * @return string
     */
    public static function getBefore(): string
    {
        return self::$before;
    }

    /**
     * @param string $text
     */
    public static function setBefore(string $text): void
    {
        self::$before = $text;
    }

    /*
    * @return array
    */
    public function toArray()
    {
        return parent::toArray();
    }
    
    /**
     * @return string
     */
    public function jsonSerialize(): array
    {
        return parent::jsonSerialize();
    }

    /**
     * @param int $options
     * @return string
     */
    public function toJson(int $options = 0)
    {
      return parent::toJson($options);
    }
}


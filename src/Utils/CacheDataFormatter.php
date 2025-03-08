<?php

namespace Silviooosilva\CacheerPhp\Utils;

/**
 * Class CacheDataFormatter
 * @author Sílvio Silva <https://github.com/silviooosilva>
 * @package Silviooosilva\CacheerPhp
 */
class CacheDataFormatter
{
    /** @param mixed $data */
    private mixed $data;

    public function __construct(mixed $data)
    {
        $this->data = $data;
    }

    /**
    * @return string|false
    */
    public function toJson()
    {
        return json_encode(
            $this->data,
            JSON_PRETTY_PRINT |
                JSON_UNESCAPED_UNICODE |
                JSON_UNESCAPED_SLASHES
        );
    }

    /**
    * @return array
    */
    public function toArray()
    {
        return (array)$this->data;
    }

    /**
    * @return string
    */
    public function toString()
    {
        return (string)$this->data;
    }

    /**
    * @return object
    */
    public function toObject()
    {
        return (object)$this->data;
    }
}

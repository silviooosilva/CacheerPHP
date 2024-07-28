<?php

namespace Silviooosilva\Utils;

class CacheDataFormatter
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function toJson()
    {
        return json_encode(
            $this->data,
            JSON_PRETTY_PRINT |
                JSON_UNESCAPED_UNICODE |
                JSON_UNESCAPED_SLASHES
        );
    }

    public function toArray()
    {
        return (array)$this->data;
    }

    public function toString()
    {
        return (string)$this->data;
    }
}

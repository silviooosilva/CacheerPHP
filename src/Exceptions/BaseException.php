<?php

declare(strict_types=1);

namespace Silviooosilva\CacheerPhp\Exceptions;

use Exception;
use JsonSerializable;

class BaseException extends Exception implements JsonSerializable
{
    /** 
     * @var array 
     * */
    protected array $details;

    public function __construct(string $message = "", int $code = 0, ?Exception $previous = null, array $details = [])
    {
        parent::__construct($message, $code, $previous);
        $this->details = $details;
    }

    /** 
     * @return array 
     * */
    public function getDetails()
    {
        return $this->details;
    }

    /** 
     * @param array $details 
     * */
    public function setDetails(array $details)
    {
        $this->details = $details;
    }

    /** 
     * @return array 
     * */
    public function toArray()
    {
        return [
            'type' => static::class,
            'message' => $this->getMessage(),
            'code' => $this->getCode(),
            'details' => $this->getDetails(),
            'file' => $this->getFile(),
            'line' => $this->getLine(),
            'trace' => $this->getTrace()
        ];
    }

    /** 
     * @return array 
     * */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /** 
     * @return string 
     * */
    public function toJson(int $options = 0)
    {
        return json_encode($this->toArray(), $options | JSON_THROW_ON_ERROR);
    }

    /** 
     * @return string 
     * */
    public function __toString()
    {
        return sprintf(
            "[%s] %s in %s on line %d\nDetails: %s",
            $this->getCode(),
            $this->getMessage(),
            $this->getFile(),
            $this->getLine(),
            json_encode($this->getDetails(), JSON_PRETTY_PRINT)
        );
    }
}

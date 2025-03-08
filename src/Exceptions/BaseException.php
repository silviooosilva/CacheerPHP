<?php

declare(strict_types=1);

namespace Silviooosilva\CacheerPhp\Exceptions;

use Exception;
use JsonSerializable;

class BaseException extends Exception implements JsonSerializable
{
    /** @param array $details */
    protected array $details;

    public function __construct(string $message = "", int $code = 0, ?Exception $previous = null, array $details = [])
    {
        parent::__construct($message, $code, $previous);
        $this->details = $details;
    }

    /** @return array */
    public function getDetails()
    {
        return $this->details;
    }

    /** @return void */
    public function setDetails(array $details)
    {
        $this->details = $details;
    }

    /** @return array */
    public function jsonSerialize(): array
    {
        return [
            'message' => $this->getMessage(),
            'code' => $this->getCode(),
            'details' => $this->getDetails(),
            'file' => $this->getFile(),
            'line' => $this->getLine(),
            'trace' => $this->getTrace()
        ];
    }

    public function __toString(): string
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

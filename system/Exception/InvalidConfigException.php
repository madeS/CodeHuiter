<?php
namespace CodeHuiter\Exception;

use Throwable;

class InvalidConfigException extends CodeHuiterRuntimeException
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, 500);
    }
}

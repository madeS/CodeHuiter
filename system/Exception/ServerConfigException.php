<?php
namespace CodeHuiter\Exception;

use Throwable;

class ServerConfigException extends CodeHuiterRuntimeException
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, 400);
    }
}

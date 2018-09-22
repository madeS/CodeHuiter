<?php
namespace CodeHuiter\Exceptions;

use Throwable;

class InvalidConfigException extends CodeHuiterException
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, 500);
    }
}

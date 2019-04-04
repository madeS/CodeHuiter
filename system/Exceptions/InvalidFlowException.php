<?php
namespace CodeHuiter\Exceptions;

use Throwable;

class InvalidFlowException extends CodeHuiterException
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, 500);
    }

    public static function onAnotherClassExpected(string $expected, string $got): InvalidFlowException
    {
        return new self(sprintf('Expected %s, got %s', $expected, $got));
    }
}

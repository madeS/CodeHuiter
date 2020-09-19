<?php
namespace CodeHuiter\Exception;

use Throwable;

class InvalidFlowException extends CodeHuiterRuntimeException
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, 500);
    }

    public static function onAnotherClassExpected(string $expected, string $got): InvalidFlowException
    {
        return new self(sprintf('Expected %s, got %s', $expected, $got));
    }

    public static function onInvalidArgument(string $argumentName, string $argumentValue): InvalidFlowException
    {
        return new self(sprintf('Invalid argument %s got [%s]', $argumentName, $argumentValue));
    }
}

<?php
namespace CodeHuiter\Exception\Runtime;

use CodeHuiter\Exception\CodeHuiterRuntimeException;
use Exception;

class DateTimeConvertException extends CodeHuiterRuntimeException
{
    public static function cantCreateDateInterval(string $string, Exception $exception): DateTimeConvertException
    {
        return new self("Cant string [$string] to DateInterval", 500, $exception);
    }

    public static function cantCreateCurrentDateTime(Exception $exception): DateTimeConvertException
    {
        return new self('Cant create current DateTime', 500, $exception);
    }

    public static function cantConvertStringToDateTime(string $string): DateTimeConvertException
    {
        return new self("Cant convert string [$string] to DateTime");
    }
}

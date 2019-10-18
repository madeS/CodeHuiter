<?php
namespace CodeHuiter\Exception\Runtime;

use CodeHuiter\Exception\CodeHuiterRuntimeException;

class EventException extends CodeHuiterRuntimeException
{
    public static function onInvalidSubscriber(string $eventClass, string $subscriberClass): EventException
    {
        return new self("Invalid subscriber [{$subscriberClass}] for event {$eventClass}");
    }

}

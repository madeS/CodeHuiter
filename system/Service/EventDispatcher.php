<?php

namespace CodeHuiter\Service;

use CodeHuiter\Service\EventDispatcher\Event;

interface EventDispatcher
{
    public function subscribe(string $eventName, string $subscriberServiceName): void;

    public function fire(Event $event): void;
}

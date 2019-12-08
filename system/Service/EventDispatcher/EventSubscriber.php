<?php

namespace CodeHuiter\Service\EventDispatcher;

interface EventSubscriber
{
    public function catchEvent(Event $event): void;
}

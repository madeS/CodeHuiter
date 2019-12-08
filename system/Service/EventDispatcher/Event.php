<?php

namespace CodeHuiter\Service\EventDispatcher;

interface Event
{
    public function getEventName(): string;
}

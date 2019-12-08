<?php

namespace CodeHuiter\Service\ByDefault\EventDispatcher;

use CodeHuiter\Service\EventDispatcher\Event;

class SimpleEvent implements Event
{
    /**
     * @var string
     */
    private $eventName;

    /**
     * @var array
     */
    private $data;

    public function __construct(string $eventName, array $data)
    {
        $this->eventName = $eventName;
        $this->data = $data;
    }

    public function getEventName(): string
    {
        return $this->eventName;
    }

    public function getData(): array
    {
        return $this->data;
    }
}

<?php

namespace CodeHuiter\Service\ByDefault\EventDispatcher;

use CodeHuiter\Config\EventsConfig;
use CodeHuiter\Database\Model;
use CodeHuiter\Service\EventDispatcher\Event;

class RelationalModelUpdatedEvent implements Event
{
    /**
     * @var string
     */
    private $eventName;

    /**
     * @var Model
     */
    private $model;

    /**
     * @var array
     */
    private $touchedSet;

    public function __construct(Model $model, array $touchedSet)
    {
        $this->eventName = EventsConfig::modelUpdated(get_class($model));
        $this->model = $model;
        $this->touchedSet = $touchedSet;
    }

    public function getEventName(): string
    {
        return $this->eventName;
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    public function getTouchedSet(): array
    {
        return $this->touchedSet;
    }
}

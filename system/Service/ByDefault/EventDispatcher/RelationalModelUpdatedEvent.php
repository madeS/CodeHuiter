<?php

namespace CodeHuiter\Service\ByDefault\EventDispatcher;

use CodeHuiter\Config\EventsConfig;
use CodeHuiter\Database\RelationalModel;
use CodeHuiter\Service\EventDispatcher\Event;

class RelationalModelUpdatedEvent implements Event
{
    /**
     * @var string
     */
    private $eventName;

    /**
     * @var RelationalModel
     */
    private $model;

    /**
     * @var array
     */
    private $touchedSet;

    public function __construct(RelationalModel $model, array $touchedSet)
    {
        $this->eventName = EventsConfig::modelUpdatedName($model->getClass());
        $this->model = $model;
        $this->touchedSet = $touchedSet;
    }

    public function getEventName(): string
    {
        return $this->eventName;
    }

    public function getModel(): RelationalModel
    {
        return $this->model;
    }

    public function getTouchedSet(): array
    {
        return $this->touchedSet;
    }
}

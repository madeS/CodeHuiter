<?php

namespace CodeHuiter\Service\ByDefault\EventDispatcher;

use CodeHuiter\Config\EventsConfig;
use CodeHuiter\Database\Model;
use CodeHuiter\Service\EventDispatcher\Event;

class RelationalModelDeletingEvent implements Event
{
    /**
     * @var string
     */
    private $eventName;

    /**
     * @var Model
     */
    private $model;

    public function __construct(Model $model)
    {
        $this->eventName = EventsConfig::modelDeleting(get_class($model));
        $this->model = $model;
    }

    public function getEventName(): string
    {
        return $this->eventName;
    }

    public function getModel(): Model
    {
        return $this->model;
    }
}

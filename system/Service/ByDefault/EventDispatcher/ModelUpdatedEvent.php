<?php

namespace CodeHuiter\Service\ByDefault\EventDispatcher;

use CodeHuiter\Config\Service\EventsConfig;
use CodeHuiter\Database\Model;
use CodeHuiter\Service\EventDispatcher\Event;

class ModelUpdatedEvent implements Event
{
    /**
     * @var string
     */
    private $eventName;

    /**
     * @var Model
     */
    private $previousModel;

    /**
     * @var Model
     */
    private $model;

    public function __construct(Model $previousModel, Model $model)
    {
        $this->eventName = EventsConfig::modelUpdated(get_class($model));
        $this->previousModel = $previousModel;
        $this->model = $model;
    }

    public function getEventName(): string
    {
        return $this->eventName;
    }

    public function getPreviousVersion(): Model
    {
        return $this->previousModel;
    }

    public function getModel(): Model
    {
        return $this->model;
    }
}

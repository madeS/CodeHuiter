<?php

namespace CodeHuiter\Service\ByDefault\EventDispatcher;

use CodeHuiter\Database\RelationalModel;
use CodeHuiter\Service\EventDispatcher\Event;

class RelationalModelDeletingEvent implements Event
{
    /**
     * @var string
     */
    private $eventName;

    /**
     * @var RelationalModel
     */
    private $model;

    public function __construct(RelationalModel $model)
    {
        $this->eventName = $model->getModelDatabaseServiceKey() . '_' . $model->getModelTable() . '.deleting';
        $this->model = $model;
    }

    public function getEventName(): string
    {
        return $this->eventName;
    }

    public function getModel(): RelationalModel
    {
        return $this->model;
    }
}

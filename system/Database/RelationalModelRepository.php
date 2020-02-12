<?php

namespace CodeHuiter\Database;

use CodeHuiter\Core\Application;
use CodeHuiter\Service\ByDefault\EventDispatcher\RelationalModelDeletingEvent;
use CodeHuiter\Service\ByDefault\EventDispatcher\RelationalModelUpdatedEvent;
use CodeHuiter\Service\DateService;
use CodeHuiter\Service\EventDispatcher;
use CodeHuiter\Service\Logger;

class RelationalModelRepository
{
    /**
     * @var Application
     */
    private $application;

    /**
     * @var RelationalModel
     */
    private $model;

    /**
     * @var RelationalDatabase
     */
    private $dbHandler;

    /**
     * @var string
     */
    private $modelClass;

    /**
     * @var string
     */
    private $table;

    /**
     * @var string[]
     */
    private $fields;

    /**
     * @var string[]
     */
    private $primaryFields;

    /**
     * @var DateService
     */
    private $dateService;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @param Application $application
     * @param RelationalModel $model
     */
    public function __construct(Application $application, RelationalModel $model)
    {
        $this->application = $application;
        $this->model = $model;
    }

    /**
     * Can be ['firstPrimaryValue', 'secondPrimaryValue']
     * Can be ['field2' => 'secondPrimaryValue', 'field1' => 'firstPrimaryValue']
     * @param string[]|int[]
     * @return RelationalModel|null
     */
    public function getById(array $primaryIdParts): ?RelationalModel
    {
        $db = $this->getDB();
        $where = [];
        foreach ($this->primaryFields as $key => $primaryField) {
            $where[$primaryField] = $primaryIdParts[$primaryField] ?? $primaryIdParts[$key];
        }
        /** @var RelationalModel|null $model */
        $model = $db->selectWhereOneObject($this->modelClass, $this->table, $where);
        if ($model !== null) {
            $this->initOriginsForModels([$model]);
        }
        return $model;
    }

    /**
     * @param array $where
     * @param array $opt
     * @return RelationalModel[]
     */
    public function find(array $where, array $opt = []): array
    {
        /** @var RelationalModel[] $models */
        $models = $this->getDB()->selectWhereObjects($this->modelClass, $this->table, $where, $opt);
        $this->initOriginsForModels($models);
        return $models;
    }

    /**
     * @param array $where
     * @param array $opt
     * @return RelationalModel|null
     */
    public function findOne(array $where, array $opt = []): ?RelationalModel
    {
        /** @var RelationalModel|null $model */
        $model = $this->getDB()->selectWhereOneObject($this->modelClass, $this->table, $where, $opt);
        if ($model) {
            $this->initOriginsForModels([$model]);
        }
        return $model;
    }

    public function save(RelationalModel $model): RelationalModel
    {
        $whereSet = $model->getPrimarySet();
        $timeString = $this->getDateService()->sqlTime();
        if ($whereSet && $model->exist()) {
            $model->updateBySet(['updated_at' => $timeString], true);
            $set = $model->getTouchedSet();
            if (!$set) {
                return $model;
            }
            $this->getDB()->update($this->table, $whereSet, $set);
        } else {
            $model->updateBySet(['updated_at' => $timeString, 'created_at' => $timeString], true);
            $set = $model->getSettledSet();
            $primaryKey = $this->getDB()->insert($this->table, $set);
            $model->setAutoIncrementField($primaryKey);
        }
        $model->initOriginals();
        $this->getEventDispatcher()->fire(new RelationalModelUpdatedEvent($model, $set));

        return $model;
    }

    /**
     * @param RelationalModel $model
     * @return bool
     */
    public function delete(RelationalModel $model): bool
    {
        $where = $model->getPrimarySet();
        if (!$where) {
            $this->getLogger()->withTag('RelationalModelRepository')->withTrace()->warning(sprintf(
                'Trying to delete not exist model [%s]',
                $model->getClass()
            ));
            return false;
        }
        // TODO Add AutoStart Transaction
        $this->getEventDispatcher()->fire(new RelationalModelDeletingEvent($model));
        return (bool)$this->getDB()->delete($this->table, $where);
    }

    public function update(array $where, array $set): void
    {
        $this->getDB()->update($this->table, $where, $set);
    }

    /**
     * @param RelationalModel[] $models
     */
    private function initOriginsForModels(array $models): void
    {
        foreach ($models as $model) {
            $model->initOriginals();
        }
    }

    /**
     * @return RelationalDatabase
     */
    private function getDB(): RelationalDatabase
    {
        if ($this->dbHandler === null) {
            $this->dbHandler = $this->application->get($this->model->getModelDatabaseServiceKey());
            $this->modelClass = $this->model->getClass();
            $this->table = $this->model->getModelTable();
            $this->fields = $this->model->getModelFields();
            $this->primaryFields = $this->model->getModelPrimaryFields();
        }
        return $this->dbHandler;
    }

    public function getDateService(): DateService
    {
        if ($this->dateService === null) {
            $this->dateService = $this->application->get(DateService::class);
        }
        return $this->dateService;
    }

    private function getLogger(): Logger
    {
        if ($this->logger === null) {
            $this->logger = $this->application->get(Logger::class);
        }
        return $this->logger;
    }

    private function getEventDispatcher(): EventDispatcher
    {
        return $this->application->get(EventDispatcher::class);
    }
}

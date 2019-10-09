<?php

namespace CodeHuiter\Database;

use CodeHuiter\Core\Application;
use CodeHuiter\Service\DateService;
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
        $where = [];
        foreach ($this->primaryFields as $key => $primaryField) {
            $where[$primaryField] = $primaryIdParts[$primaryField] ?? $primaryIdParts[$key];
        }

        /** @var RelationalModel|null $model */
        $model = $this->getDB()->selectWhereOneObject($this->modelClass, $this->table, $where);
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
        $this->initOriginsForModels([$model]);
        return $model;
    }



    public function save(RelationalModel $model): RelationalModel
    {
        $whereSet = $model->getPrimarySet();
        $touchedSet = $model->getTouchedSet();
        if ($whereSet && !$touchedSet) {
            return $model;
        }
        if ($whereSet) {
            $this->getDB()->update($this->table, $whereSet, $touchedSet);
        } else {
            $primaryKey = $this->getDB()->insert($this->table, $touchedSet);
            $model->setAutoIncrementField($primaryKey);
        }
        $model->initOriginals();
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
        return (bool)$this->getDB()->delete($this->table, $where);
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

    private function getDateService(): DateService
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
}

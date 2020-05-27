<?php

namespace CodeHuiter\Database;

use CodeHuiter\Config\Module\RelationalRepositoryConfig;
use CodeHuiter\Core\Application;
use CodeHuiter\Service\ByDefault\EventDispatcher\RelationalModelDeletingEvent;
use CodeHuiter\Service\ByDefault\EventDispatcher\RelationalModelUpdatedEvent;
use CodeHuiter\Service\DateService;
use CodeHuiter\Service\EventDispatcher;
use CodeHuiter\Service\Logger;

class RelationalRepository
{
    /**
     * @var Application
     */
    private $application;

    /**
     * @var RelationalDatabaseHandler
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
     * @var RelationalRepositoryConfig
     */
    private $config;

    public function __construct(Application $application, RelationalRepositoryConfig $config)
    {
        $this->application = $application;
        $this->config = $config;
    }

    public function getConfig(): RelationalRepositoryConfig
    {
        return $this->config;
    }

    /**
     * Can be ['firstPrimaryValue', 'secondPrimaryValue']
     * Can be ['field2' => 'secondPrimaryValue', 'field1' => 'firstPrimaryValue']
     * @param string[]|int[]
     * @return Model|null
     */
    public function getById(array $primaryIdParts): ?Model
    {
        $db = $this->getDB();
        $where = [];
        foreach ($this->primaryFields as $key => $primaryField) {
            $where[$primaryField] = $primaryIdParts[$primaryField] ?? $primaryIdParts[$key];
        }
        /** @var Model|null $model */
        $model = $db->selectWhereOneObject($this->modelClass, $this->table, $where);
        if ($model !== null) {
            $this->initOriginsForModels([$model]);
        }
        return $model;
    }

    /**
     * @param array $where
     * @param array $opt
     * @return Model[]
     */
    public function find(array $where, array $opt = []): array
    {
        /** @var Model[] $models */
        $models = $this->getDB()->selectWhereObjects($this->modelClass, $this->table, $where, $opt);
        $this->initOriginsForModels($models);
        return $models;
    }

    /**
     * @param array $where
     * @param array $opt
     * @return Model|null
     */
    public function findOne(array $where, array $opt = []): ?Model
    {
        /** @var Model|null $model */
        $model = $this->getDB()->selectWhereOneObject($this->modelClass, $this->table, $where, $opt);
        if ($model) {
            $this->initOriginsForModels([$model]);
        }
        return $model;
    }

    public function exist(Model $model): bool
    {
        foreach ($this->config->primaryFields as $field) {
            if (!$model->getModelOriginalField($field)) {
                return false;
            }
        }
        return true;
    }

    public function save(Model $model): Model
    {
        $whereSet = $this->getPrimarySet($model);
        $timeString = $this->getDateService()->sqlTime();
        if ($whereSet && $this->exist($model)) {
            $model->updateModelBySet(['updated_at' => $timeString], true);
            $set = $model->getModelTouchedSet();
            if (!$set) {
                return $model;
            }
            $this->getDB()->update($this->table, $whereSet, $set);
        } else {
            $model->updateModelBySet(['updated_at' => $timeString, 'created_at' => $timeString], true);
            $set = $model->getModelSettledSet();
            $primaryKey = $this->getDB()->insert($this->table, $set);
            $model->updateModelBySet([$this->config->autoIncrementField => $primaryKey]);
        }
        $model->initModelOriginals();
        $this->getEventDispatcher()->fire(new RelationalModelUpdatedEvent($model, $set));

        return $model;
    }

    /**
     * @param Model $model
     * @return bool
     */
    public function delete(Model $model): bool
    {
        $where = $this->getPrimarySet($model);
        if (!$where) {
            $this->getLogger()->withTag('RelationalModelRepository')->withTrace()->warning(
                sprintf('Trying to delete not exist model [%s] with table [%s]', get_class($model), $this->config->table)
            );
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
     * @param Model[] $models
     */
    private function initOriginsForModels(array $models): void
    {
        foreach ($models as $model) {
            $model->initModelOriginals();
        }
    }

    /**
     * Return [primaryField => value] map or null if dont set
     * @param Model $model
     * @return array|null
     */
    private function getPrimarySet(Model $model): ?array
    {
        $set = [];
        $isOriginalInitialized = $model->isModelOriginalInitialized();
        foreach ($this->config->primaryFields as $field) {
            if ($isOriginalInitialized && $model->getModelOriginalField($field)) {
                $set[$field] = $model->getModelOriginalField($field);
            } elseif ($model->getModelField($field)) {
                $set[$field] = $model->getModelField($field);
            } else {
                return null;
            }
        }
        return $set;
    }

    /**
     * @return RelationalDatabaseHandler
     */
    private function getDB(): RelationalDatabaseHandler
    {
        if ($this->dbHandler === null) {
            $this->dbHandler = $this->application->get($this->config->dbServiceName);
            $this->modelClass = $this->config->modelClass;
            $this->table = $this->config->table;
            $this->primaryFields = $this->config->primaryFields;
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

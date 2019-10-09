<?php

namespace CodeHuiter\Database;

use CodeHuiter\Config\Config;
use CodeHuiter\Core\Application;
use CodeHuiter\Service\DateService;

class RelationalModel
{
    /** @var string */
    protected static $databaseServiceKey = 'db';

    /** @var string */
    protected static $table = 'tableName';

    /** @var string */
    protected static $autoIncrementField = 'id';

    /** @var string[] */
    protected static $primaryFields = ['id'];

    /** @var string[] */
    protected static $fields;

    /** @var RelationalDatabase */
    protected static $dbHandler;

    /** @var DateService */
    private static $dateService;

    /**
     * @var array
     */
    protected $_origins;

    /**
     * @return self
     */
    public static function getEmpty(): self
    {
        $model = new static();
        $model->initOriginals();
        return $model;
    }

    /**
     * @return RelationalDatabase
     *@deprecated TODO replace to repositories
     */
    protected static function getDb(): RelationalDatabase
    {
        if (static::$dbHandler === null) {
            static::$dbHandler = Application::getInstance()->get(static::$databaseServiceKey);
        }
        return static::$dbHandler;
    }

    /**
     * @return string
     */
    public function getModelDatabaseServiceKey(): string
    {
        return static::$databaseServiceKey;
    }

    /**
     * @return string
     */
    public function getModelTable(): string
    {
        return static::$table;
    }

    /**
     * @return string[]
     */
    public function getModelFields(): array
    {
        if (static::$fields === null) {
            static::$fields = [];
            foreach ($this as $field => $value) {
                if ($field[0] !== '_') {
                    static::$fields[] = $field;
                }
            }
        }
        return static::$fields;
    }

    public function initOriginals(): void
    {
        $fields = $this->getModelFields();
        foreach ($fields as $field) {
            $this->_origins[$field] = $this->$field;
        }
    }

    /**
     * @return array
     */
    public function getModelPrimaryFields(): array
    {
        return static::$primaryFields;
    }

    /**
     * Return [primaryField => value] map or null if dont set
     * @return array|null
     */
    public function getPrimarySet(): ?array
    {
        $set = [];
        $isOriginalInitialized = $this->_origins;
        foreach (static::$primaryFields as $field) {
            if ($isOriginalInitialized && $this->_origins[$field]) {
                $set[$field] = $this->_origins[$field];
            } elseif ($this->$field) {
                $set[$field] = $this->$field;
            } else {
                return null;
            }
        }
        return $set;
    }

    /**
     * @return array
     */
    public function getTouchedSet(): array
    {
        $set = [];
        $isOriginalInitialized = $this->_origins;
        foreach (static::$fields as $field) {
            if ($isOriginalInitialized && $this->_origins[$field] !== $this->$field) {
                $set[$field] = $this->$field;
            }
        }
        return $set;
    }

    /**
     * @param string $autoIncrement
     */
    public function setAutoIncrementField(string $autoIncrement): void
    {
        $field = static::$autoIncrementField;
        $this->$field = $autoIncrement;
    }

    /**
     * Is Model exist in DB (only for initOrigins Models)
     * Use Repositories for getModels
     * @return bool
     */
    public function exist(): bool
    {
        foreach (static::$primaryFields as $field) {
            if (!$this->_origins[$field]) {
                return false;
            }
        }
        return true;
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return static::class;
    }

    /**
     * @deprecated TODO Replace to repositories
     * @return DateService
     */
    protected static function getDateService(): DateService
    {
        if (static::$dateService === null) {
            static::$dateService = Application::getInstance()->get(DateService::class);
        }
        return static::$dateService;
    }

    /**
     * @deprecated
     * @param array $where Where Key-Value array
     * @param array $opt [order => [[ field=>string, reverse=>bool ],...]]
     * @return self|null
     */
    public static function getOneWhere($where = [], $opt = []): ?self
    {
        /** @var self|null $model */
        $model = static::getDb()->selectWhereOneObject(static::class, static::$table, $where, $opt);
        return $model;
    }

    /**
     * @deprecated
     * @param array $where Where Key-Value array
     * @param array $opt [key=>field, order => [[ field=>string, reverse=>bool ],...], limit=>[count=>,from=>,page=>,per_page=>]]
     * @return self[]
     */
    public static function getWhere($where = [], $opt = []): array
    {
        /** @var self[] $model */
        $model = static::getDb()->selectWhereObjects(static::class, static::$table, $where, $opt);
        return $model;
    }

    /**
     * @deprecated
     * @param array $set Data
     * @return string Primary Key
     */
    public static function insert($set): string
    {
        return static::getDb()->insert(static::$table, $set);
    }

    /** @var array  */
    protected $_touchedFields = [];

    /**
     * @param string $fieldName
     */
    protected function touch(string $fieldName): void
    {
        if (!in_array($fieldName, $this->_touchedFields, true)) {
            $this->_touchedFields[] = $fieldName;
        }
    }

    /**
     * @param bool $onlyTouched
     * @return self
     */
    public function save(bool $onlyTouched = false): self
    {
        $filledPrimaryKeys = true;
        $whereArray = [];
        foreach (static::$primaryFields as $field) {
            if (!$this->$field) {
                $filledPrimaryKeys = false;
            }
            $whereArray[$field] = $this->$field;
        }

        $setArray = [];
        $fields = $onlyTouched && $filledPrimaryKeys ? $this->_touchedFields : $this->getModelFields();
        foreach ($fields as $field) {
            $setArray[$field] = $this->$field;
        }
        if (!$setArray) {
            return $this;
        }

        $db = static::getDb();

        if ($filledPrimaryKeys && $db->selectWhereOneObject(static::class, static::$table, $whereArray)) {
            $db->update(static::$table, $whereArray, $setArray);
            return $this;
        }

        $lastInsertId = $db->insert(static::$table, $setArray);
        foreach (static::$primaryFields as $field) {
            $whereArray[$field] = $lastInsertId;
        }
        /** @var self $object */
        $object = $db->selectWhereOneObject(static::class, static::$table, $whereArray);
        return $object;
    }

    /**
     * @param array $setArray
     */
    public function update(array $setArray): void
    {
        $whereArray = [];
        foreach (static::$primaryFields as $field) {
            $whereArray[$field] = $this->$field;
        }
        if ($whereArray) {
            $db = static::getDb();
            $db->update(static::$table, $whereArray, $setArray);
        }
    }

    /**
     * @return void
     */
    public function delete(): void
    {
        $whereArray = [];
        foreach (static::$primaryFields as $field) {
            $whereArray[$field] = $this->$field;
        }
        if ($whereArray) {
            $db = static::getDb();
            $db->delete(static::$table, $whereArray);
        }
    }
}

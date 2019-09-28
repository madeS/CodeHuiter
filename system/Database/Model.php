<?php

namespace CodeHuiter\Database;

use CodeHuiter\Config\Config;
use CodeHuiter\Core\Application;
use CodeHuiter\Service\DateService;

class Model
{
    /** @var string */
    protected static $database = 'db';

    /** @var string */
    protected static $table = 'tableName';

    /** @var string[] */
    protected static $primaryFields = ['id'];

    /** @var string[] */
    protected static $fields = [];

    /** @var AbstractDatabase */
    private static $dbHandler;

    /** @var DateService */
    private static $dateService;

    /**
     * @deprecated TODO replace to repositories
     * @return AbstractDatabase
     */
    protected static function getDb(): AbstractDatabase
    {
        if (static::$dbHandler === null) {
            static::$dbHandler = Application::getInstance()->get(static::$database);
        }
        return static::$dbHandler;
    }

    /**
     * @return AbstractDatabase
     */
    public function getModelDB(): AbstractDatabase
    {
        return self::getDb();
    }

    /**
     * @return string
     */
    public function getModelTable(): string
    {
        return self::$table;
    }

    /**
     * @return string[]
     */
    public function getModelFields(): array
    {
        return self::$fields;
    }

    /**
     * @return array
     */
    public function getModelPrimaryFields(): array
    {
        return self::$primaryFields;
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return self::class;
    }

    /**
     * @deprecated TODO Replace to repositories
     * @return DateService
     */
    protected static function getDateService(): DateService
    {
        if (static::$dateService === null) {
            static::$dateService = Application::getInstance()->get(Config::SERVICE_KEY_DATE);
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
        $fields = $onlyTouched && $filledPrimaryKeys ? $this->_touchedFields : static::$fields;
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

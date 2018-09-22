<?php

namespace CodeHuiter\Database;

use CodeHuiter\Core\Application;
use CodeHuiter\Exceptions\AppContainerException;
use CodeHuiter\Services\DateService;

class Model
{
    /** @var string */
    protected static $database = 'db';

    /** @var string */
    protected static $table = 'tableName';

    /** @var string  */
    protected static $select = '*';

    /** @var string[] */
    protected static $primaryKeys = ['id'];

    /** @var string[] */
    protected static $fields = [];

    /** @var AbstractDatabase */
    protected static $dbHndlr = null;

    /** @var DateService */
    protected static $dateService = null;

    /**
     * @return AbstractDatabase
     */
    protected static function getDb()
    {
        if (static::$dbHndlr === null) {
            static::$dbHndlr = Application::getInstance()->get(static::$database);
        }
        return static::$dbHndlr;
    }

    protected static function getDateService()
    {
        if (static::$dateService === null) {
            static::$dateService = Application::getInstance()->get('date');
        }
        return static::$dateService;
    }

    /**
     * @param array $where Where Key-Value array
     * @param array $opt [order => [[ field=>string, reverse=>bool ],...]]
     * @return self
     */
    public static function getOneWhere($where = [], $opt = [])
    {
        return static::getDb()->selectWhereOneObject(static::class, static::$table, $where, $opt);
    }

    /**
     * @param array $where Where Key-Value array
     * @param array $opt [key=>field, order => [[ field=>string, reverse=>bool ],...], limit=>[count=>,from=>,page=>,per_page=>]]
     * @return self[]
     */
    public static function getWhere($where = [], $opt = [])
    {
        return static::getDb()->selectWhereObjects(static::class, static::$table, $where, $opt);
    }

    /**
     * @param array $set Data
     * @return string Primary Key
     */
    public static function insert($set)
    {
        return static::getDb()->insert(static::$table, $set);
    }

    /**
     * @return self
     */
    public function save()
    {
        $filledPrimaryKeys = true;
        $whereArray = [];
        foreach (static::$primaryKeys as $field) {
            if (!$field) {
                $filledPrimaryKeys = false;
            }
            $whereArray[$field] = $this->$field;
        }

        $setArray = [];
        foreach (static::$fields as $field) {
            $setArray[$field] = $this->$field;
        }

        $db = static::getDb();

        if ($filledPrimaryKeys && $db->selectWhereOneObject(static::class, static::$table, $whereArray)) {
            $db->update(static::$table, $setArray, $whereArray);
            return $this;
        }

        $lastInsertId = $db->insert(static::$table, $setArray);
        foreach (static::$primaryKeys as $field) {
            $whereArray[$field] = $lastInsertId;
            break;
        }

        return $db->selectWhereOneObject(static::class, static::$table, $whereArray);
    }

    public function update($setArray)
    {
        $whereArray = [];
        foreach (static::$primaryKeys as $field) {
            $whereArray[$field] = $this->$field;
        }
        if ($whereArray) {
            $db = static::getDb();
            $db->update(static::$table, $setArray, $whereArray);
        }
    }

}

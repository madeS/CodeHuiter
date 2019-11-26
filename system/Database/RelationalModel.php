<?php

namespace CodeHuiter\Database;

use CodeHuiter\Exception\CodeHuiterRuntimeException;

abstract class RelationalModel
{
    /** @var string */
    protected $_databaseServiceKey = 'db';

    /** @var string */
    protected $_table = 'tableName';

    /** @var string */
    protected $_autoIncrementField = 'id';

    /** @var string[] */
    protected $_primaryFields = ['id'];

    /** @var string[] Autofilled */
    private $_fields;

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
     * @return string
     */
    public function getModelDatabaseServiceKey(): string
    {
        return $this->_databaseServiceKey;
    }

    /**
     * @return string
     */
    public function getModelTable(): string
    {
        return $this->_table;
    }

    /**
     * @return string[]
     */
    public function getModelFields(): array
    {
        if ($this->_fields === null) {
            $this->_fields = [];
            foreach ($this as $field => $value) {
                if ($field[0] !== '_') {
                    $this->_fields[] = $field;
                }
            }
        }
        return $this->_fields;
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
        return $this->_primaryFields;
    }

    /**
     * Return [primaryField => value] map or null if dont set
     * @return array|null
     */
    public function getPrimarySet(): ?array
    {
        $set = [];
        $isOriginalInitialized = $this->_origins;
        foreach ($this->_primaryFields as $field) {
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
        foreach ($this->getModelFields()  as $field) {
            if ($isOriginalInitialized && $this->_origins[$field] !== $this->$field) {
                $set[$field] = $this->$field;
            }
        }
        return $set;
    }

    /**
     * @return array
     */
    public function getSettledSet(): array
    {
        $set = [];
        foreach ($this->getModelFields() as $field) {
            if ($this->$field !== null) {
                $set[$field] = $this->$field;
            }
        }
        return $set;
    }

    /**
     * @param array $set
     * @param bool $optional
     */
    public function updateBySet(array $set, bool $optional = false): void
    {
        $fields = $this->getModelFields();
        foreach ($set as $key => $value) {
            if (in_array($key, $fields, true)) {
                $this->$key = $value;
            } elseif (!$optional) {
                throw new CodeHuiterRuntimeException(sprintf('Model %s does not have field %s',  self::class, $key));
            }
        }
    }

    public function getPrivateField(string $key): ?string
    {
        return $this->$key ?? null;
    }

    public function setPrivateField(string $key, string $value): void
    {
        if (isset($this->$key)) {
            $this->$key = $value;
        }
    }

    /**
     * @param string $autoIncrement
     */
    public function setAutoIncrementField(string $autoIncrement): void
    {
        $field = $this->_autoIncrementField;
        if ($field) {
            $this->$field = $autoIncrement;
        }
    }

    /**
     * Is Model exist in DB (only for initOrigins Models)
     * Use Repositories for getModels
     * @return bool
     */
    public function exist(): bool
    {
        foreach ($this->_primaryFields as $field) {
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
}

<?php

namespace CodeHuiter\Database;

use CodeHuiter\Exception\CodeHuiterRuntimeException;

abstract class Model
{
    /**
     * @var string[] Autofilled
     */
    private $_fields;

    /**
     * @var array Origin fields values
     */
    protected $_origins;

    /**
     * @return self
     */
    public static function emptyModel(): self
    {
        $model = new static();
        $model->initModelOriginals();
        return $model;
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

    public function initModelOriginals(): void
    {
        $fields = $this->getModelFields();
        foreach ($fields as $field) {
            $this->_origins[$field] = $this->$field;
        }
    }

    /**
     * @return array
     */
    public function getModelTouchedSet(): array
    {
        $set = [];
        $isOriginalInitialized = $this->_origins;
        foreach ($this->getModelFields() as $field) {
            if ($isOriginalInitialized && $this->_origins[$field] !== $this->$field) {
                $set[$field] = $this->$field;
            }
        }
        return $set;
    }

    /**
     * @return array
     */
    public function getModelSettledSet(): array
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
    public function updateModelBySet(array $set, bool $optional = false): void
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

    /**
     * @param string $field
     * @return mixed|null
     */
    public function getModelField(string $field)
    {
        return $this->$field ?? null;
    }

    /**
     * @param string $field
     * @return mixed|null
     */
    public function getModelOriginalField(string $field)
    {
        return $this->_origins[$field] ?? null;
    }

    public function isModelOriginalInitialized(): bool
    {
        return !empty($this->_origins);
    }
}

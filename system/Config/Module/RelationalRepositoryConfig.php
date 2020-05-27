<?php

namespace CodeHuiter\Config\Module;

class RelationalRepositoryConfig
{
    /**
     * @var string
     */
    public $modelClass;

    /**
     * @var string
     */
    public $dbServiceName;

    /**
     * @var string
     */
    public $table;

    /**
     * @var string
     */
    public $autoIncrementField;

    /**
     * @var string[]
     */
    public $primaryFields;

    public function __construct(
        string $modelClass,
        string $dbServiceName,
        string $table,
        string $autoIncrementField = 'id',
        array $primaryFields = ['id']
    ) {
        $this->modelClass = $modelClass;
        $this->dbServiceName = $dbServiceName;
        $this->table = $table;
        $this->autoIncrementField = $autoIncrementField;
        $this->primaryFields = $primaryFields;
    }
}

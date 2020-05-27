<?php
namespace CodeHuiter\Database\Handlers;

use CodeHuiter\Config\RelationalDatabaseConfig;
use CodeHuiter\Database\RelationalDatabaseHandler;
use CodeHuiter\Exception\CodeHuiterRuntimeException;
use CodeHuiter\Exception\DatabaseException;
use CodeHuiter\Service\Logger;
use PDO;
use PDOException;
use PDOStatement;
use Throwable;

class PDORelationalDatabaseHandler implements RelationalDatabaseHandler
{
    /**
     * @var PDO $connection
     */
    protected $connection;

    /**
     * @var int
     */
    protected $transactionLevel = 0;

    /**
     * @var RelationalDatabaseConfig
     */
    protected $config;

    /**
     * @var DatabaseProfiler
     */
    protected $profiler;

    public function __construct(Logger $log, RelationalDatabaseConfig $databaseConfig)
    {
        $this->profiler = new DatabaseProfiler($log, $databaseConfig);
        $this->connect($databaseConfig);
    }

    /**
     * @param RelationalDatabaseConfig $config
     */
    protected function connect(RelationalDatabaseConfig $config): void
    {
        $this->config = $config;
        $options = [
            PDO::ATTR_PERSISTENT => $config->persistent,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ];
        if ($config->charset && $config->collate) {
            $options[PDO::MYSQL_ATTR_INIT_COMMAND] = "SET NAMES {$config->charset} COLLATE {$config->collate}";
        }
        try {
            $this->connection = new PDO($config->dsn, $config->username, $config->password, $options);
        } catch (PDOException $exception) {
            throw DatabaseException::onPDOConnect($exception, $config);
        }
        if ($config->persistent) {
            if ($config->charset && $config->collate) {
                $this->connection->exec("SET NAMES {$config->charset} COLLATE {$config->collate}");
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function quote($string): string
    {
        return $this->connection->quote($string);
    }

    /**
     * {@inheritdoc}
     */
    public function disconnect(): void
    {
        $this->connection->exec('SELECT pg_terminate_backend(pg_backend_pid());');
        $this->connection = null;
    }

    private function reconnectProblem(PDOException $exception): bool
    {
        if (!$this->config->reconnect) {
            return false;
        }

        $messages = array(
            'SQLSTATE[HY000]: General error: 2006 MySQL server has gone away',
            'SQLSTATE[HY000] [2013] Lost connection to MySQL server at \'reading initial communication packet\', system error: 110',
            'SQLSTATE[HY000]: General error: 2013 Lost connection to MySQL server during query'
        );

        if(in_array($exception->getMessage(), $messages))
        {
            $this->connection = null;
            $this->connect($this->config);
            return true;
        }

        return false;
    }

    private function executeStatement(string $query, array $params): PDOStatement
    {
        try {
            $statement = $this->connection->prepare($query);
            $statement->execute($params);
        } catch (PDOException $exception) {
            if (!$this->reconnectProblem($exception)) {
                throw $this->pdoException($exception, $query, $params);
            }
            $statement = $this->connection->prepare($query);
            $statement->execute($params);
        } catch (Throwable $event) {
            throw $this->pdoException($event, $query, $params);
        }
        return $statement;
    }

    /**
     * {@inheritdoc}
     */
    public function selectObjects(?string $className, string $query, array $params = [], ?string $fieldAsKey = null): array
    {
        $this->profiler->isEnabled && $this->profiler->preExecution();
        $statement = $this->executeStatement($query, $params);
        $this->profiler->isEnabled && $this->profiler->preFormatting();

        if ($className === null) {
            $statement->setFetchMode(PDO::FETCH_OBJ);
        } else {
            $statement->setFetchMode(PDO::FETCH_CLASS, $className);
        }
        $result = $statement->fetchAll();
        if ($fieldAsKey !== null) {
            $fieldAsKeyResult = [];
            foreach ($result as $resultItem) {
                $fieldAsKeyResult[$resultItem->$fieldAsKey] = $resultItem;
            }
            $result = $fieldAsKeyResult;
        }

        $this->profiler->isEnabled && $this->profiler->done($query, $params);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function selectOneObject(?string $className, string $query, array $params = [])
    {
        $this->profiler->isEnabled && $this->profiler->preExecution();
        $statement = $this->executeStatement($query, $params);
        $this->profiler->isEnabled && $this->profiler->preFormatting();

        if ($className === null) {
            $statement->setFetchMode(PDO::FETCH_OBJ);
        } else {
            $statement->setFetchMode(PDO::FETCH_CLASS, $className);
        }
        $result = $statement->fetch();

        $this->profiler->isEnabled && $this->profiler->done($query, $params);

        if (!$result) $result = null;
        return $result;
    }

    /**
     * @inheritDoc
     */
    public function select(string $query, array $params = [], ?string $fieldAsKey = null): array
    {
        $this->profiler->isEnabled && $this->profiler->preExecution();
        $statement = $this->executeStatement($query, $params);
        $this->profiler->isEnabled && $this->profiler->preFormatting();

        $statement->setFetchMode(PDO::FETCH_ASSOC);
        $result = $statement->fetchAll();
        if ($fieldAsKey !== null) {
            $fieldAsKeyResult = [];
            foreach ($result as $resultItem) {
                $fieldAsKeyResult[$resultItem[$fieldAsKey]] = $resultItem;
            }
            $result = $fieldAsKeyResult;
        }

        $this->profiler->isEnabled && $this->profiler->done($query, $params);

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function selectOne(string $query, array $params = []): ?array
    {
        $this->profiler->isEnabled && $this->profiler->preExecution();
        $statement = $this->executeStatement($query, $params);
        $this->profiler->isEnabled && $this->profiler->preFormatting();

        // Format
        $statement->setFetchMode(PDO::FETCH_ASSOC);
        $result = $statement->fetch();

        $this->profiler->isEnabled && $this->profiler->done($query, $params);

        if (!$result) $result = null;
        return $result;
    }

    /**
     * @inheritDoc
     */
    public function selectOneField(string $query, array $params = [], ?string $field = null): ?string
    {
        $this->profiler->isEnabled && $this->profiler->preExecution();
        $statement = $this->executeStatement($query, $params);
        $this->profiler->isEnabled && $this->profiler->preFormatting();

        $statement->setFetchMode(PDO::FETCH_BOTH);
        $ret = $statement->fetch();
        $result = null;
        if ($ret) {
            if ($field === null) {
                $result = $ret[0];
            } else {
                $result = $ret[$field];
            }
        }

        $this->profiler->isEnabled && $this->profiler->done($query, $params);

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function selectField(string $query, array $params, string $field, ?string $fieldAsKey = null): array
    {
        $this->profiler->isEnabled && $this->profiler->preExecution();
        $statement = $this->executeStatement($query, $params);
        $this->profiler->isEnabled && $this->profiler->preFormatting();

        $statement->setFetchMode(PDO::FETCH_ASSOC);
        $tmpResult = $statement->fetchAll();

        $result = [];
        if ($fieldAsKey !== false) {
            foreach ($tmpResult as $resultItem) {
                $result[$resultItem[$fieldAsKey]] = $resultItem[$field];
            }
        } else {
            foreach ($tmpResult as $resultItem) {
                $result[] = $resultItem[$field];
            }
        }

        $this->profiler->isEnabled && $this->profiler->done($query, $params);

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function execute(string $query, array $params = [], bool $returnInsertedId = false): int
    {
        $this->profiler->isEnabled && $this->profiler->preExecution();

        $result = null;
        if ($returnInsertedId === true) {
            // Execute
            if ($this->transactionLevel === 0) {
                $this->connection->beginTransaction();
            }
            $this->executeStatement($query, $params);
            // Format
            $result = $this->connection->lastInsertId();
            if ($this->transactionLevel === 0) {
                $this->connection->commit();
            }
            if ($this->transactionLevel === 0 && $this->connection->inTransaction()) {
                $this->connection->rollBack();
                throw new CodeHuiterRuntimeException('Direction start of transaction is not supported');
            }
        } else {
            $statement = $this->executeStatement($query, $params);
            // Format
            $result = $statement->rowCount();
        }

        $this->profiler->isEnabled && $this->profiler->preFormatting();
        $this->profiler->isEnabled && $this->profiler->done($query, $params);

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function selectWhereObjects(?string $className, string $table, array $where, array $opt = []): array
    {
        $compiled = self::arrayCompile(
            $where, null, null,
            ($opt['order'] ?? null),
            ($opt['limit'] ?? null)
        );
        return $this->selectObjects(
            $className,
            "SELECT * FROM `{$table}` \n WHERE {$compiled['where']} {$compiled['order']} {$compiled['limit']}",
            $compiled['params'],
            ($opt['key'] ?? null)
        );
    }

    /**
     * @inheritDoc
     */
    public function selectWhereOneObject(?string $className, string $table, array $where, array $opt = [])
    {
        $compiled = self::arrayCompile(
            $where, null, null, ($opt['order'] ?? null), null
        );
        return $this->selectOneObject(
            $className,
            "SELECT * FROM `{$table}` \n WHERE {$compiled['where']} {$compiled['order']} LIMIT 0,1",
            $compiled['params']
        );
    }

    /**
     * @inheritDoc
     */
    public function selectWhere(string $table, array $where, array $opt = []): array
    {
        $compiled = self::arrayCompile(
            $where, null, null,
            ($opt['order'] ?? null),
            ($opt['limit'] ?? null)
        );
        return $this->select(
            "SELECT * FROM `{$table}` \n WHERE {$compiled['where']} {$compiled['order']} {$compiled['limit']}",
            $compiled['params'],
            ($opt['key'] ?? null)
        );
    }

    /**
     * @inheritDoc
     */
    public function selectWhereOne(string $table, array $where, array $opt = []): ?array
    {
        $compiled = self::arrayCompile(
            $where, null, null, ($opt['order'] ?? null), null
        );
        return $this->selectOne(
            "SELECT * FROM `{$table}` \n WHERE {$compiled['where']} {$compiled['order']} LIMIT 0,1",
            $compiled['params']
        );
    }

    /**
     * @inheritDoc
     */
    public function selectFieldWhere(string $table, array $where, string $field, array $opt = []): array
    {
        $compiled = self::arrayCompile(
            $where, null, null,
            ($opt['order'] ?? null),
            ($opt['limit'] ?? null)
        );
        return $this->selectField(
            "SELECT * FROM `{$table}` \n WHERE {$compiled['where']} {$compiled['order']} {$compiled['limit']}",
            $compiled['params'],
            $field,
            ($opt['key'] ?? null)
        );
    }

    /**
     * @inheritDoc
     */
    public function insert(string $table, array $set): string
    {
        if (!$set) {
            throw new CodeHuiterRuntimeException('Trying to insert with no set');
        }
        $compiled = self::arrayCompile(null, null, $set, null, null);
        return $this->execute(
            "INSERT INTO `{$table}` \n ({$compiled['insert_keys']}) \n VALUES ({$compiled['insert_values']})",
            $compiled['params'],
            true
        );
    }

    /**
     * @inheritDoc
     */
    public function update(string $table, array $where, array $set): int
    {
        $compiled = self::arrayCompile($where, $set, null, null, null);
        return $this->execute(
            "UPDATE `{$table}` \n SET {$compiled['set']} \n WHERE {$compiled['where']}",
            $compiled['params']
        );
    }

    /**
     * @inheritDoc
     */
    public function delete(string $table, array $where): int
    {
        $compiled = self::arrayCompile($where, null, null, null, null);
        /** @noinspection SqlWithoutWhere */
        return $this->execute(
            "DELETE FROM `{$table}` \n WHERE {$compiled['where']}",
            $compiled['params']
        );
    }

    public function transactionStart(): void
    {
        if ($this->transactionLevel === 0) {
            $this->connection->beginTransaction();
            $this->transactionLevel++;
        }
    }

    public function transactionCommit(): void
    {
        if ($this->transactionLevel > 0) {
            $this->transactionLevel--;
            if ($this->transactionLevel === 0) {
                $this->connection->commit();
            }
        }
    }

    public function transactionRollBack(): void
    {
        if ($this->transactionLevel > 0) {
            $this->connection->rollBack();
            $this->transactionLevel = 0;
        }
    }

    public function getBenchmarkData(): array
    {
        return $this->profiler->getBenchmarkData();
    }

    private function pdoException(Throwable $exception, string $query, array $params): PDOException
    {
        return new PDOException("{$exception->getMessage()} \n with query: '$query' with params: " . json_encode($params), 0, $exception);
    }

    /**
     * @param array|null $whereArray
     * @param array|null $setArray
     * @param array|null $insertArray
     * @param array|null $orderArray ['field1' => 'asc', 'field2' => 'desc']
     * @param array|null $limitArray [count => int or 'all', from => int] or [page => int, per_page => int]
     * @return array ['where' => string, 'set' => string, 'insert' => string, 'params' => array]
     */
    private static function arrayCompile(
        ?array $whereArray,
        ?array $setArray = null,
        ?array $insertArray = null,
        ?array $orderArray = null,
        ?array $limitArray = null
    ): array {
        $result = [];
        $pdoParams = [];

        if ($whereArray) {
            $sqlWherePartArray = [];
            foreach ($whereArray as $keys => $value) {
                $sqlWhereKeyPartArray = [];
                $keysArray = explode(',', $keys);
                foreach ($keysArray as $strKey) {
                    if (is_array($value)) {
                        $specialWhere = false;
                        if (isset($value['>'])) {
                            $sqlWhereKeyPartArray[] = " `{$strKey}` > :w_{$strKey} ";
                            $pdoParams[":w_{$strKey}"] = $value['>'];
                            $specialWhere = true;
                        }
                        if (isset($value['<'])) {
                            $sqlWhereKeyPartArray[] = " `{$strKey}` < :w_{$strKey} ";
                            $pdoParams[":w_{$strKey}"] = $value['<'];
                            $specialWhere = true;
                        }
                        if (isset($value['like'])) {
                            $sqlWhereKeyPartArray[] = " `{$strKey}` LIKE :w_{$strKey} ";
                            $pdoParams[":w_{$strKey}"] = $value['like'];
                            $specialWhere = true;
                        }
                        if (!$specialWhere) {
                            $tmpSqlArr = [];
                            foreach ($value as $valueIndex => $valueItem) {
                                $tmpSqlArr[] = " :w_{$strKey}_{$valueIndex} ";
                                $pdoParams[":w_{$strKey}_{$valueIndex}"] = $valueItem;
                            }
                            if ($tmpSqlArr) {
                                $sqlWhereKeyPartArray[] = " `{$strKey}` IN(" . implode(',', $tmpSqlArr) . ') ';
                            } else {
                                $sqlWhereKeyPartArray[] = ' 0 ';
                            }
                        }
                    } else {
                        $sqlWhereKeyPartArray[] = " `{$strKey}` = :w_{$strKey} ";
                        $pdoParams[":w_{$strKey}"] = $value;
                    }
                }
                $sqlWherePartArray[] = implode(' OR ', $sqlWhereKeyPartArray);
            }
            $result['where'] = implode(' AND ', $sqlWherePartArray);
        } else {
            $result['where'] = ' 1 ';
        }

        if ($setArray) {
            $sqlSetPartArray = [];
            foreach ($setArray as $strKey => $value) {
                $sqlSetPartArray[] = " `{$strKey}` = :s_{$strKey} ";
                $pdoParams[":s_{$strKey}"] = $value;
            }
            $result['set'] = implode(' , ', $sqlSetPartArray);
        }

        if ($insertArray) {
            $sqlInsertKeysPartArray = [];
            $sqlInsertValuesPartArray = [];
            foreach ($insertArray as $strKey => $value) {
                $sqlInsertKeysPartArray[] = " `{$strKey}` ";
                $sqlInsertValuesPartArray[] = " :i_{$strKey} ";
                $pdoParams[":i_{$strKey}"] = $value;
            }
            $result['insert_keys'] = implode(' , ', $sqlInsertKeysPartArray);
            $result['insert_values'] = implode(' , ', $sqlInsertValuesPartArray);
        }

        $result['order'] = $orderArray ? self::sqlOrder($orderArray) : '';
        $result['limit'] = $limitArray ? self::sqlLimit($limitArray) : '';

        $result['params'] = $pdoParams;

        return $result;
    }

    /**
     * @param array $options ['field1' => 'asc', 'field2' => 'desc']
     * @return string
     */
    private static function sqlOrder(array $options): string
    {
        $orderArrays = [];
        foreach ($options as $field => $orderValue) {
            $orderValue = strtolower($orderValue);
            if (is_string($field) && in_array($orderValue, ['asc', 'desc'], true)) {
                $orderArrays[] = " `{$field}` " . ($orderValue === 'asc' ? 'ASC' : 'DESC');
            }
        }
        if (!$orderArrays)  {
            return '';
        }
        return ' ORDER BY ' . implode(',', $orderArrays) . ' ';
    }

    /**
     * @param array $options [count => int or 'all', from => int] or [page => int, per_page => int]
     * @return string
     */
    public static function sqlLimit(array $options): string
    {
        $sqlLimit = '';
        $from = 0;
        $count = 'all';
        if (isset($options['count']) && $options['count'] && $options['count'] !== 'all' ) {
            $count = isset($options['count']) ? (int)$options['count'] : 0;
            if ($count < 0) $count = 0;
        }
        if (isset($options['from']) && $options['from']) {
            $from = isset($options['from']) ? (int)$options['from'] : 0;
            if ($from < 0) $from = 0;
        }
        if (isset($options['page']) && $options['page'] && isset($options['per_page']) && $options['per_page']) {
            $from = ($options['page']-1) * $options['per_page'];
            if ($from < 0) $from = 0;
            $count = $options['per_page'];
            if ($count < 0) $count = 0;
        }
        if ($count !== 'all') $sqlLimit = " LIMIT $from, $count ";
        return $sqlLimit;
    }
}

<?php
namespace CodeHuiter\Database\Drivers;

use CodeHuiter\Config\RelationalDatabaseConfig;
use CodeHuiter\Database\ByDefault\AbstractDatabase;
use CodeHuiter\Exception\CodeHuiterRuntimeException;
use PDO;
use PDOException;
use Throwable;

class PDODriver extends AbstractDatabase
{
    /** @var PDO $connection */
    protected $connection;

    /** @var int */
    protected $transactionLevel = 0;

    /**
     * @var RelationalDatabaseConfig
     */
    protected $config = null;

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
        $this->connection = new PDO($config->dsn, $config->username, $config->password, $options);
        if ($config->persistent) {
            if ($config->charset && $config->collate) {
                $this->connection->query("SET NAMES {$config->charset} COLLATE {$config->collate}");
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
        $this->connection->query('SELECT pg_terminate_backend(pg_backend_pid());');
        $this->connection = null;
    }

    protected function reconnectProblem(PDOException $exception): bool
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

    /**
     * {@inheritdoc}
     */
    public function selectObjects($className, $query, $params = [], $fieldAsKey = false): array
    {
        $startTime = $this->isCalculateTime ? microtime(true) : 0;

        // Execute
        try {
            $statement = $this->connection->prepare($query);
            $statement->execute($params);
        } catch (PDOException $exception) {
            if ($this->reconnectProblem($exception)) {
                $statement = $this->connection->prepare($query);
                $statement->execute($params);
                $query = 'reconnected: ' . $query;
            } else {
                throw $this->pdoException($exception, $query, $params);
            }
        } catch (Throwable $event) {
            throw $this->pdoException($event, $query, $params);
        }

        $executeTime = $this->isCalculateTime ? microtime(true) : 0;

        // Format
        if ($className === false) {
            $statement->setFetchMode(PDO::FETCH_OBJ, $className);
        } else {
            $statement->setFetchMode(PDO::FETCH_CLASS, $className);
        }
        $result = $statement->fetchAll();
        if ($fieldAsKey !== false) {
            $fieldAsKeyResult = [];
            foreach ($result as $resultItem) {
                $fieldAsKeyResult[$resultItem->$fieldAsKey] = $resultItem;
            }
            $result = $fieldAsKeyResult;
        }

        if ($this->isCalculateTime) {
            $finishTime = microtime(true);
            if ($this->isDebug === true) {
                $this->holdQueryBenchmark($query, $params, $executeTime - $startTime, $finishTime - $executeTime);
            }
            if ($this->isLogIfLonger !== false && ($finishTime - $startTime > $this->isLogIfLonger / 1000)) {
                $this->logQueryBenchmark($query, $params, $executeTime - $startTime, $finishTime - $executeTime);
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function selectOneObject($className, $query, $params = [])
    {
        $startTime = $this->isCalculateTime ? microtime(true) : 0;

        // Execute
        try {
            $statement = $this->connection->prepare($query);
            $statement->execute($params);
        } catch (PDOException $exception) {
            if ($this->reconnectProblem($exception)) {
                $statement = $this->connection->prepare($query);
                $statement->execute($params);
                $query = 'reconnected: ' . $query;
            } else {
                throw $this->pdoException($exception, $query, $params);
            }
        } catch (Throwable $event) {
            throw $this->pdoException($event, $query, $params);
        }

        $executeTime = $this->isCalculateTime ? microtime(true) : 0;

        // Format
        if ($className === false) {
            $statement->setFetchMode(PDO::FETCH_OBJ, $className);
        } else {
            $statement->setFetchMode(PDO::FETCH_CLASS, $className);
        }
        $result = $statement->fetch();

        if ($this->isCalculateTime) {
            $finishTime = microtime(true);
            if ($this->isDebug === true) {
                $this->holdQueryBenchmark($query, $params, $executeTime - $startTime, $finishTime - $executeTime);
            }
            if ($this->isLogIfLonger !== false && ($finishTime - $startTime > $this->isLogIfLonger / 1000)) {
                $this->logQueryBenchmark($query, $params, $executeTime - $startTime, $finishTime - $executeTime);
            }
        }

        if (!$result) $result = null;
        return $result;
    }

    /**
     * @inheritDoc
     */
    public function select($query, $params = [], $fieldAsKey = false): array
    {
        $startTime = $this->isCalculateTime ? microtime(true) : 0;

        // Execute
        try {
            $statement = $this->connection->prepare($query);
            $statement->execute($params);
        } catch (PDOException $exception) {
            if ($this->reconnectProblem($exception)) {
                $statement = $this->connection->prepare($query);
                $statement->execute($params);
                $query = 'reconnected: ' . $query;
            } else {
                throw $this->pdoException($exception, $query, $params);
            }
        } catch (Throwable $event) {
            throw $this->pdoException($event, $query, $params);
        }

        $executeTime = $this->isCalculateTime ? microtime(true) : 0;

        // Format
        $statement->setFetchMode(PDO::FETCH_ASSOC);
        $result = $statement->fetchAll();
        if ($fieldAsKey !== false) {
            $fieldAsKeyResult = [];
            foreach ($result as $resultItem) {
                $fieldAsKeyResult[$resultItem[$fieldAsKey]] = $resultItem;
            }
            $result = $fieldAsKeyResult;
        }

        if ($this->isCalculateTime) {
            $finishTime = microtime(true);
            if ($this->isDebug === true) {
                $this->holdQueryBenchmark($query, $params, $executeTime - $startTime, $finishTime - $executeTime);
            }
            if ($this->isLogIfLonger !== false && ($finishTime - $startTime > $this->isLogIfLonger / 1000)) {
                $this->logQueryBenchmark($query, $params, $executeTime - $startTime, $finishTime - $executeTime);
            }
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function selectOne($query, $params = []): ?array
    {
        $startTime = $this->isCalculateTime ? microtime(true) : 0;

        // Execute
        try {
            $statement = $this->connection->prepare($query);
            $statement->execute($params);
        } catch (PDOException $exception) {
            if ($this->reconnectProblem($exception)) {
                $statement = $this->connection->prepare($query);
                $statement->execute($params);
                $query = 'reconnected: ' . $query;
            } else {
                throw $this->pdoException($exception, $query, $params);
            }
        } catch (Throwable $event) {
            throw $this->pdoException($event, $query, $params);
        }

        $executeTime = $this->isCalculateTime ? microtime(true) : 0;

        // Format
        $statement->setFetchMode(PDO::FETCH_ASSOC);
        $result = $statement->fetch();

        if ($this->isCalculateTime) {
            $finishTime = microtime(true);
            if ($this->isDebug === true) {
                $this->holdQueryBenchmark($query, $params, $executeTime - $startTime, $finishTime - $executeTime);
            }
            if ($this->isLogIfLonger !== false && ($finishTime - $startTime > $this->isLogIfLonger / 1000)) {
                $this->logQueryBenchmark($query, $params, $executeTime - $startTime, $finishTime - $executeTime);
            }
        }
        if (!$result) $result = null;
        return $result;
    }

    /**
     * @inheritDoc
     */
    public function selectOneField($query, $params = [], $field = null): ?string
    {
        $startTime = $this->isCalculateTime ? microtime(true) : 0;

        // Execute
        try {
            $statement = $this->connection->prepare($query);
            $statement->execute($params);
        } catch (PDOException $exception) {
            if ($this->reconnectProblem($exception)) {
                $statement = $this->connection->prepare($query);
                $statement->execute($params);
                $query = 'reconnected: ' . $query;
            } else {
                throw $this->pdoException($exception, $query, $params);
            }
        } catch (Throwable $event) {
            throw $this->pdoException($event, $query, $params);
        }

        $executeTime = $this->isCalculateTime ? microtime(true) : 0;

        // Format
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

        if ($this->isCalculateTime) {
            $finishTime = microtime(true);
            if ($this->isDebug === true) {
                $this->holdQueryBenchmark($query, $params, $executeTime - $startTime, $finishTime - $executeTime);
            }
            if ($this->isLogIfLonger !== false && ($finishTime - $startTime > $this->isLogIfLonger / 1000)) {
                $this->logQueryBenchmark($query, $params, $executeTime - $startTime, $finishTime - $executeTime);
            }
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function selectField($query, $params = [], $field = null, $fieldAsKey = false): array
    {
        $startTime = $this->isCalculateTime ? microtime(true) : 0;

        // Execute
        try {
            $statement = $this->connection->prepare($query);
            $statement->execute($params);
        } catch (PDOException $exception) {
            if ($this->reconnectProblem($exception)) {
                $statement = $this->connection->prepare($query);
                $statement->execute($params);
                $query = 'reconnected: ' . $query;
            } else {
                throw $this->pdoException($exception, $query, $params);
            }
        } catch (Throwable $event) {
            throw $this->pdoException($event, $query, $params);
        }

        $executeTime = $this->isCalculateTime ? microtime(true) : 0;

        // Format
        $statement->setFetchMode(PDO::FETCH_ASSOC);
        $tmpResult = $statement->fetchAll();

        if ($field === null) {
            $field = 0;
        }
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

        if ($this->isCalculateTime) {
            $finishTime = microtime(true);
            if ($this->isDebug === true) {
                $this->holdQueryBenchmark($query, $params, $executeTime - $startTime, $finishTime - $executeTime);
            }
            if ($this->isLogIfLonger !== false && ($finishTime - $startTime > $this->isLogIfLonger / 1000)) {
                $this->logQueryBenchmark($query, $params, $executeTime - $startTime, $finishTime - $executeTime);
            }
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function execute($query, $params = [], $insertedResult = false): int
    {
        $startTime = $this->isCalculateTime ? microtime(true) : 0;

        $result = null;
        if ($insertedResult === true) {
            // Execute
            if ($this->transactionLevel === 0) {
                $this->connection->beginTransaction();
            }
            try {
                $statement = $this->connection->prepare($query);
                $statement->execute($params);
            } catch (PDOException $exception) {
                if ($this->reconnectProblem($exception)) {
                    $statement = $this->connection->prepare($query);
                    $statement->execute($params);
                    $query = 'reconnected: ' . $query;
                } else {
                    throw $this->pdoException($exception, $query, $params);
                }
            } catch (Throwable $event) {
                throw $this->pdoException($event, $query, $params);
            }
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
            // Execute
            try {
                $statement = $this->connection->prepare($query);
                $statement->execute($params);
            } catch (PDOException $exception) {
                if ($this->reconnectProblem($exception)) {
                    $statement = $this->connection->prepare($query);
                    $statement->execute($params);
                    $query = 'reconnected: ' . $query;
                } else {
                    throw $this->pdoException($exception, $query, $params);
                }
            } catch (Throwable $event) {
                throw $this->pdoException($event, $query, $params);
            }
            // Format
            $result = $statement->rowCount();
        }

        $executeTime = $this->isCalculateTime ? microtime(true) : 0;

        if ($this->isCalculateTime) {
            $finishTime = microtime(true);
            if ($this->isDebug === true) {
                $this->holdQueryBenchmark($query, $params, $executeTime - $startTime, $finishTime - $executeTime);
            }
            if ($this->isLogIfLonger !== false && ($finishTime - $startTime > $this->isLogIfLonger / 1000)) {
                $this->logQueryBenchmark($query, $params, $executeTime - $startTime, $finishTime - $executeTime);
            }
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function selectWhereObjects($className, $table, $where, $opt = []): array
    {
        $compiled = self::arrayCompile(
            $where, null, null,
            (isset($opt['order']) ? $opt['order'] : null),
            (isset($opt['limit']) ? $opt['limit'] : null)
        );
        return $this->selectObjects(
            $className,
            "SELECT * FROM `{$table}` \n WHERE {$compiled['where']} {$compiled['order']} {$compiled['limit']}",
            $compiled['params'],
            (isset($opt['key']) ? $opt['key'] : false)
        );
    }

    /**
     * @inheritDoc
     */
    public function selectWhereOneObject($className, $table, $where, $opt = [])
    {
        $compiled = self::arrayCompile(
            $where, null, null, (isset($opt['order']) ? $opt['order'] : null), null
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
    public function selectWhere($table, $where, $opt = []): array
    {
        $compiled = self::arrayCompile(
            $where, null, null,
            (isset($opt['order']) ? $opt['order'] : null),
            (isset($opt['limit']) ? $opt['limit'] : null)
        );
        return $this->select(
            "SELECT * FROM `{$table}` \n WHERE {$compiled['where']} {$compiled['order']} {$compiled['limit']}",
            $compiled['params'],
            (isset($opt['key']) ? $opt['key'] : false)
        );
    }

    /**
     * @inheritDoc
     */
    public function selectWhereOne($table, $where, $opt = []): ?array
    {
        $compiled = self::arrayCompile(
            $where, null, null, (isset($opt['order']) ? $opt['order'] : null), null
        );
        return $this->selectOne(
            "SELECT * FROM `{$table}` \n WHERE {$compiled['where']} {$compiled['order']} LIMIT 0,1",
            $compiled['params']
        );
    }

    /**
     * @inheritDoc
     */
    public function selectFieldWhere($table, $where, $field = null, $opt = []): array
    {
        $compiled = self::arrayCompile(
            $where, null, null,
            (isset($opt['order']) ? $opt['order'] : null),
            (isset($opt['limit']) ? $opt['limit'] : null)
        );
        return $this->selectField(
            "SELECT * FROM `{$table}` \n WHERE {$compiled['where']} {$compiled['order']} {$compiled['limit']}",
            $compiled['params'],
            $field,
            (isset($opt['key']) ? $opt['key'] : false)
        );
    }

    /**
     * @inheritDoc
     */
    public function insert($table, $set): string
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
    public function update($table, $where, $set): int
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
    public function delete($table, $where): int
    {
        $compiled = self::arrayCompile($where, null, null, null, null);
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

    private function pdoException(Throwable $exception, string $query, array $params): PDOException
    {
        return new PDOException("{$exception->getMessage()} \n with query: '$query' with params: " . json_encode($params), 0, $exception);
    }

    /**
     * @param array|null $whereArray
     * @param array|null $setArray
     * @param array|null $insertArray
     * @param array|null $orderArray
     * @param array|null $limitArray
     * @return array ['where' => string, 'set' => string, 'insert' => string, 'params' => array]
     */
    protected static function arrayCompile(
        $whereArray,
        $setArray = null,
        $insertArray = null,
        $orderArray = null,
        $limitArray = null
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
                                $sqlWhereKeyPartArray[] = " `{$strKey}` IN(" . implode(',', $tmpSqlArr) . ") ";
                            } else {
                                $sqlWhereKeyPartArray[] = " 0 ";
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
     * @param array $orderArray ['field1' => 'asc', 'field2' => 'desc']
     * @return string
     */
    public static function sqlOrder(array $orderArray): string
    {
        $orderArrays = [];
        foreach ($orderArray as $field => $orderValue) {
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
     * @param array $limitArray [count => int, from => int, page => int, per_page => int]
     * @return string
     */
    public static function sqlLimit(array $limitArray): string
    {
        $sqlLimit = '';
        $from = 0;
        $count = 'all';
        if (isset($limitArray['count']) && $limitArray['count'] && $limitArray['count'] !== 'all' ) {
            $count = isset($limitArray['count']) ? (int)$limitArray['count'] : 0;
            if ($count < 0) $count = 0;
        }
        if (isset($limitArray['from']) && $limitArray['from']) {
            $from = isset($limitArray['from']) ? (int)$limitArray['from'] : 0;
            if ($from < 0) $from = 0;
        }
        if (isset($limitArray['page']) && $limitArray['page'] && isset($limitArray['per_page']) && $limitArray['per_page']) {
            $from = ($limitArray['page']-1) * $limitArray['per_page'];
            if ($from < 0) $from = 0;
            $count = $limitArray['per_page'];
            if ($count < 0) $count = 0;
        }
        if ($count !== 'all') $sqlLimit = " LIMIT $from, $count ";
        return $sqlLimit;
    }
}

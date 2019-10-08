<?php
namespace CodeHuiter\Database\ByDefault;

use CodeHuiter\Config\RelationalDatabaseConfig;
use CodeHuiter\Database\RelationalDatabase;
use CodeHuiter\Service\Logger;

abstract class AbstractDatabase implements RelationalDatabase
{
    /** @var Logger */
    protected $log;

    protected $benchmarkData = [];

    protected $isLogIfLonger;

    protected $isDebug;

    protected $isLogTrace;

    protected $isCalculateTime;

    public function __construct(Logger $log, RelationalDatabaseConfig $databaseConfig)
    {
        $this->log = $log;
        $this->isLogIfLonger = $databaseConfig->logIfLonger;
        $this->isDebug = $databaseConfig->debug;
        $this->isCalculateTime = ($this->isDebug === true) || ($this->isLogIfLonger !== false);
        $this->isLogTrace = $databaseConfig->logTrace;
        $this->connect($databaseConfig);
    }

    abstract protected function connect(RelationalDatabaseConfig $config): void;

    abstract public function disconnect(): void;

    public function getBenchmarkData(): array
    {
        return $this->benchmarkData;
    }

    protected function holdQueryBenchmark($query, $params, $timeExecute, $timeFormat)
    {
        $this->benchmarkData[] = [
            'query' => $query,
            'params' => $params,
            'time_execute' => $timeExecute,
            'time_format' => $timeFormat,
            'time_total' => $timeExecute + $timeFormat,
        ];
    }

    protected function logQueryBenchmark($query, $params, $timeExecute, $timeFormat) {
        $data = [
            'query' => $query,
            'params' => $params,
            'time_execute' => $timeExecute,
            'time_format' => $timeFormat,
            'time_total' => $timeExecute + $timeFormat,
        ];
        if ($this->isLogTrace === true) {
            $this->log->withTrace();
        }
        $this->log->withTag('DB_QUERY_TIME');
        $this->log->warning("Database query time is {$data['time_total']}", $data);
    }

    /**
     * @param string $className ClassName for stdObject
     * @param string $query SQL query @lang MySQL
     * @param array $params Key-Value params
     * @param bool $fieldAsKey Field as key in result
     * @return \stdClass[]
     */
    abstract public function selectObjects($className, $query, $params = [], $fieldAsKey = false): array;

    /**
     * @param string $query SQL query @lang MySQL
     * @param array $params Key-Value params
     * @param string|bool $fieldAsKey Field as key in result
     * @return array
     */
    abstract public function select($query, $params = [], $fieldAsKey = false): array;

    /**
     * @param string $className ClassName or null for stdObject
     * @param string $query SQL query @lang MySQL
     * @param array $params Key-Value params
     * @return \stdClass|null
     */
    abstract public function selectOneObject($className, $query, $params = []);

    /**
     * @param string $query SQL query @lang MySQL
     * @param array $params Key-Value params
     * @return array|null
     */
    abstract public function selectOne($query, $params = []): ?array ;

    /**
     * @param string $query SQL query @lang MySQL
     * @param array $params Key-Value params
     * @param string|null $field field to extract or null for first value extract
     * @return string|null
     */
    abstract public function selectOneField($query, $params = [], $field = null): ?string;

    /**
     * @param string $query SQL query @lang MySQL
     * @param array $params Key-Value params
     * @param string|null $field field to extract or null for first value extract
     * @param string|bool $fieldAsKey Field as key in result
     * @return string[]
     */
    abstract public function selectField($query, $params = [], $field = null, $fieldAsKey = false): array;

    /**
     * @param string $query SQL query @lang MySQL
     * @param array $params Key-Value params
     * @return int affected rows
     */
    abstract public function execute($query, $params = []): int;

    /**
     * @param string|null $className ClassName
     * @param string $table Table name
     * @param array $where Where Key-Value array
     * @param array $opt [key'=>field, order => [[ field=>string, reverse=>bool ],...], limit=>[count=>,from=>,page=>,per_page=>]]
     * @return \stdClass[]
     */
    abstract public function selectWhereObjects($className, $table, $where, $opt = []): array;

    /**
     * @param string|null $className ClassName
     * @param string $table Table name
     * @param array $where Where Key-Value array
     * @param array $opt [order => [[ field=>string, reverse=>bool ],...]]
     * @return \stdClass|null
     */
    abstract public function selectWhereOneObject($className, $table, $where, $opt = []);

    /**
     * @param string $table Table name
     * @param array $where Where Key-Value array
     * @param array $opt ['key'=>field, order => [[ field=>string, reverse=>bool ],...], limit=>[count=>,from=>,page=>,per_page=>]]
     * @return array[]
     */
    abstract public function selectWhere($table, $where, $opt = []): array;

    /**
     * @param string $table Table name
     * @param array $where Where Key-Value array
     * @param array $opt [order => [[ field=>string, reverse=>bool ],...]]
     * @return array|null
     */
    abstract public function selectWhereOne($table, $where, $opt = []): ?array;

    /**
     * @param string $table Table name
     * @param array $where Where Key-Value array
     * @param string|null $field field to extract or null for first value extract
     * @param array $opt ['key'=>field, order => [[ field=>string, reverse=>bool ],...], limit=>[count=>,from=>,page=>,per_page=>]]
     * @return string[]
     */
    abstract public function selectFieldWhere($table, $where, $field = null, $opt = []): array;
    /**
     * @param string $table Table name
     * @param array $set SetMap Key-Value array
     * @return string Primary Key Last Increment
     */
    abstract public function insert($table, $set): string;

    /**
     * @param string $table Table name
     * @param array $where Where Key-Value array
     * @param array $set SetMap Key-Value array
     * @return int affected rows
     */
    abstract public function update($table, $where, $set): int;

    /**
     * @param string $table Table name
     * @param array $where Where Key-Value array
     * @return int affected rows
     */
    abstract public function delete($table, $where): int;

    abstract public function transactionStart(): void;
    abstract public function transactionCommit(): void;
    abstract public function transactionRollBack(): void;

    /**
     * @param $string
     * @return string
     */
    abstract public function quote($string): string;
}

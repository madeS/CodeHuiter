<?php
namespace CodeHuiter\Database;

use CodeHuiter\Config\DatabaseConfig;
use CodeHuiter\Core\Log\AbstractLog;

abstract class AbstractDatabase
{
    /** @var AbstractLog */
    protected $log;

    protected $benchmarkData = [];

    protected $isLogIfLonger;

    protected $isDebug;

    protected $isLogTrace;

    protected $isCalculateTime;

    public function __construct(AbstractLog $log, DatabaseConfig $databaseConfig)
    {
        $this->log = $log;
        $this->isLogIfLonger = $databaseConfig->logIfLonger;
        $this->isDebug = $databaseConfig->debug;
        $this->isCalculateTime = ($this->isDebug === true) || ($this->isLogIfLonger !== false);
        $this->isLogTrace = $databaseConfig->logTrace;
        $this->connect($databaseConfig);
    }

    abstract protected function connect(DatabaseConfig $config);

    abstract public function disconnect();

    public function getBenchmarkData()
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
            $this->log->addTrace();
        }
        $this->log->warning("Database query time is {$data['time_total']}", $data, 'DB_QUERY_TIME');
    }

    /**
     * @param string $className ClassName or False for stdObject
     * @param string $query SQL query @lang MySQL
     * @param array $params Key-Value params
     * @param bool $fieldAsKey Field as key in result
     * @return \stdClass[]
     */
    abstract public function selectObjects($className, $query, $params = [], $fieldAsKey = false);

    /**
     * @param string $query SQL query @lang MySQL
     * @param array $params Key-Value params
     * @param string|bool $fieldAsKey Field as key in result
     * @return array
     */
    abstract public function select($query, $params = [], $fieldAsKey = false);

    /**
     * @param string $className ClassName or False for stdObject
     * @param string $query SQL query @lang MySQL
     * @param array $params Key-Value params
     * @return \stdClass
     */
    abstract public function selectOneObject($className, $query, $params = []);

    /**
     * @param string $query SQL query @lang MySQL
     * @param array $params Key-Value params
     * @return array
     */
    abstract public function selectOne($query, $params = []);

    /**
     * @param string $query SQL query @lang MySQL
     * @param array $params Key-Value params
     * @param string|null $field field to extract or null for first value extract
     * @return string
     */
    abstract public function selectOneField($query, $params = [], $field = null);

    /**
     * @param string $query SQL query @lang MySQL
     * @param array $params Key-Value params
     * @param string|null $field field to extract or null for first value extract
     * @param string|bool $fieldAsKey Field as key in result
     * @return array
     */
    abstract public function selectField($query, $params = [], $field = null, $fieldAsKey = false);

    /**
     * @param string $query SQL query @lang MySQL
     * @param array $params Key-Value params
     * @return int affected rows
     */
    abstract public function execute($query, $params = []);

    /**
     * @param string|null $className ClassName
     * @param string $table Table name
     * @param array $where Where Key-Value array
     * @param array $opt [key'=>field, order => [[ field=>string, reverse=>bool ],...], limit=>[count=>,from=>,page=>,per_page=>]]
     * @return \stdClass[]
     */
    abstract public function selectWhereObjects($className, $table, $where, $opt = []);

    /**
     * @param string|null $className ClassName
     * @param string $table Table name
     * @param array $where Where Key-Value array
     * @param array $opt [order => [[ field=>string, reverse=>bool ],...]]
     * @return \stdClass|false
     */
    abstract public function selectWhereOneObject($className, $table, $where, $opt = []);

    /**
     * @param string $table Table name
     * @param array $where Where Key-Value array
     * @param array $opt ['key'=>field, order => [[ field=>string, reverse=>bool ],...], limit=>[count=>,from=>,page=>,per_page=>]]
     * @return array[]
     */
    abstract public function selectWhere($table, $where, $opt = []);

    /**
     * @param string $table Table name
     * @param array $where Where Key-Value array
     * @param array $opt [order => [[ field=>string, reverse=>bool ],...]]
     * @return array|false
     */
    abstract public function selectWhereOne($table, $where, $opt = []);

    /**
     * @param string $table Table name
     * @param array $where Where Key-Value array
     * @param string|null $field field to extract or null for first value extract
     * @param array $opt ['key'=>field, order => [[ field=>string, reverse=>bool ],...], limit=>[count=>,from=>,page=>,per_page=>]]
     * @return string
     */
    abstract public function selectFieldWhere($table, $where, $field = null, $opt = []);
    /**
     * @param string $table Table name
     * @param array $set SetMap Key-Value array
     * @return string Primary Key Last Increment
     */
    abstract public function insert($table, $set);

    /**
     * @param string $table Table name
     * @param array $where Where Key-Value array
     * @param array $set SetMap Key-Value array
     * @return int affected rows
     */
    abstract public function update($table, $where, $set);

    /**
     * @param string $table Table name
     * @param array $where Where Key-Value array
     * @return int affected rows
     */
    abstract public function delete($table, $where);

    abstract public function transactionStart();
    abstract public function transactionCommit();
    abstract public function transactionRollBack();

    /**
     * @param $string
     * @return string
     */
    abstract public function quote($string);
}

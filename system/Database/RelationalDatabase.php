<?php
namespace CodeHuiter\Database;

interface RelationalDatabase
{
    public function disconnect(): void;

    public function getBenchmarkData(): array;

    /**
     * @param string $className ClassName for stdObject
     * @param string $query SQL query @lang MySQL
     * @param array $params Key-Value params
     * @param bool $fieldAsKey Field as key in result
     * @return \stdClass[]
     */
    public function selectObjects($className, $query, $params = [], $fieldAsKey = false): array;

    /**
     * @param string $query SQL query @lang MySQL
     * @param array $params Key-Value params
     * @param string|bool $fieldAsKey Field as key in result
     * @return array
     */
    public function select($query, $params = [], $fieldAsKey = false): array;

    /**
     * @param string $className ClassName or null for stdObject
     * @param string $query SQL query @lang MySQL
     * @param array $params Key-Value params
     * @return \stdClass|null
     */
    public function selectOneObject($className, $query, $params = []);

    /**
     * @param string $query SQL query @lang MySQL
     * @param array $params Key-Value params
     * @return array|null
     */
    public function selectOne($query, $params = []): ?array;

    /**
     * @param string $query SQL query @lang MySQL
     * @param array $params Key-Value params
     * @param string|null $field field to extract or null for first value extract
     * @return string|null
     */
    public function selectOneField($query, $params = [], $field = null): ?string;

    /**
     * @param string $query SQL query @lang MySQL
     * @param array $params Key-Value params
     * @param string|null $field field to extract or null for first value extract
     * @param string|bool $fieldAsKey Field as key in result
     * @return string[]
     */
    public function selectField($query, $params = [], $field = null, $fieldAsKey = false): array;

    /**
     * @param string $query SQL query @lang MySQL
     * @param array $params Key-Value params
     * @return int affected rows
     */
    public function execute($query, $params = []): int;

    /**
     * @param string|null $className ClassName
     * @param string $table Table name
     * @param array $where Where Key-Value array
     * @param array $opt [key'=>field, order => [[ field=>string, reverse=>bool ],...], limit=>[count=>,from=>,page=>,per_page=>]]
     * @return \stdClass[]
     */
    public function selectWhereObjects($className, $table, $where, $opt = []);

    /**
     * @param string|null $className ClassName
     * @param string $table Table name
     * @param array $where Where Key-Value array
     * @param array $opt [order => [[ field=>string, reverse=>bool ],...]]
     * @return \stdClass|null
     */
    public function selectWhereOneObject($className, $table, $where, $opt = []);

    /**
     * @param string $table Table name
     * @param array $where Where Key-Value array
     * @param array $opt ['key'=>field, order => [[ field=>string, reverse=>bool ],...], limit=>[count=>,from=>,page=>,per_page=>]]
     * @return array[]
     */
    public function selectWhere($table, $where, $opt = []): array;

    /**
     * @param string $table Table name
     * @param array $where Where Key-Value array
     * @param array $opt [order => [[ field=>string, reverse=>bool ],...]]
     * @return array|null
     */
    public function selectWhereOne($table, $where, $opt = []): ?array;

    /**
     * @param string $table Table name
     * @param array $where Where Key-Value array
     * @param string|null $field field to extract or null for first value extract
     * @param array $opt ['key'=>field, order => [[ field=>string, reverse=>bool ],...], limit=>[count=>,from=>,page=>,per_page=>]]
     * @return string[]
     */
    public function selectFieldWhere($table, $where, $field = null, $opt = []): array;

    /**
     * @param string $table Table name
     * @param array $set SetMap Key-Value array
     * @return string Primary Key Last Increment
     */
    public function insert($table, $set): string;

    /**
     * @param string $table Table name
     * @param array $where Where Key-Value array
     * @param array $set SetMap Key-Value array
     * @return int affected rows
     */
    public function update($table, $where, $set): int;

    /**
     * @param string $table Table name
     * @param array $where Where Key-Value array
     * @return int affected rows
     */
    public function delete($table, $where): int;

    public function transactionStart(): void;
    public function transactionCommit(): void;
    public function transactionRollBack(): void;

    /**
     * @param $string
     * @return string
     */
    public function quote($string): string;
}

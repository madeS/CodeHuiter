<?php

namespace CodeHuiter\Facilities\SearchList\MultiTableSearcher;

use CodeHuiter\Core\Application;
use CodeHuiter\Core\Request;
use CodeHuiter\Database\Handlers\PDORelationalDatabaseHandler;
use CodeHuiter\Database\RelationalDatabaseHandler;
use CodeHuiter\Database\RelationalRepository;
use CodeHuiter\Modifier\StringModifier;
use CodeHuiter\Facilities\SearchList\SearchListResult;
use stdClass;

/**
 * This is legacy code. Dont use this code class, if it is not legacy
 */
class MultiTableSearcher
{
    /**
     * @var Application
     */
    protected $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    public function acceptFilters(Request $request, array $additionalFields = []): array
    {
        $query = $request->getGet('query');
        $filters = [];
        if ($query) {
            $filters['query'] = $query;
        }
        foreach ($additionalFields as $additionalField => $defaultValue) {
            $fieldValue = $request->getGet($additionalField);
            if ($fieldValue) {
                $filters[$additionalField] = $fieldValue;
            } elseif ($defaultValue) {
                $filters[$additionalField] = $defaultValue;
            }
        }
        return $filters;
    }

    public function acceptPages(Request $request, int $perPage = 20): array
    {
        $pages = [];
        $pages['per_page'] = $perPage;
        $currentPage = $request->getGet('page');
        $pages['page'] = $currentPage ?: 1;
        return $pages;
    }

    protected $sqls_table = 'table';
    protected $sqls_extend = ['data_info'];
    protected $sqls_connect = array();
    protected $sqls_select = ' * ';
    protected $sqls_from = ' table ';
    protected $sqls_where = ' 1 ';
    protected $sqls_order = ' ORDER BY id ASC ';
    protected $sqls_after = '';// = " {#sql} ";

    protected $bindings = [];

    protected function searchFinish(
        RelationalRepository $repository,
        array $options = [],
        array $filters = [],
        array $pages = [],
        bool $requireResultCount = false
    ): SearchListResult {

        if (isset($options['ids'])){
            $ids = array();
            $ids[] = 0;
            foreach($options['ids'] as $val) {
                $some_id = (int)$val;
                if ($some_id > 0 && !in_array($some_id, $ids, true)) $ids[] = $some_id;
            }
            $this->sqls_where .= " AND {$this->sqls_table}.id IN (".  implode(',', $ids).") ";
        }


        $sql_limit = PDORelationalDatabaseHandler::sqlLimit($pages);
        $resultSql = "SELECT {$this->sqls_select} FROM {$this->sqls_from} WHERE {$this->sqls_where} {$this->sqls_order} $sql_limit ";
        if ($this->sqls_after){
            $resultSql = StringModifier::replace($this->sqls_after, array('{#sql}' => $resultSql));
            $this->sqls_after = '';
        }

        /** @var RelationalDatabaseHandler $dbHandler */
        $dbHandler = $this->application->get($repository->getConfig()->dbServiceName);
        $ret = $dbHandler->selectObjects($repository->getConfig()->modelClass, $resultSql, $this->bindings);

        foreach($ret as $index => $r){
            foreach ($this->sqls_connect as $inside){
                $this->resultInside($ret[$index], 'inn_'.$inside.'_', $inside);
            }
            foreach($this->sqls_extend as $extend){
                if (isset($ret[$index]->$extend)){
                    $ret[$index]->$extend = ($ret[$index]->$extend) ? @json_decode($ret[$index]->$extend,true) : array();
                }
            }
        }

        $itemsCount = null;
        if ($requireResultCount) {
            $sqlCount = 'COUNT(*)';
            $countQuery = "SELECT {$sqlCount} as counter FROM {$this->sqls_from} WHERE {$this->sqls_where} ";
            $itemsCount = $dbHandler->selectOneField($countQuery, $this->bindings, 'counter');
        }

        return new SearchListResult($ret, $filters, $pages, $itemsCount);
    }

    protected function searchFinishOne(
        RelationalRepository $repository,
        array $options = []
    ): ?stdClass {
        if (isset($options['id']) && $options['id'] !== null){
            //$this->sqls_where .= " AND {$this->sqls_table}.id = {$this->mm->sqlInt($options['id'],0)} ";
            $this->sqls_where .= " AND {$this->sqls_table}.id = :{$this->sqls_table}_id ";
            $this->bindings[":{$this->sqls_table}_id"] = $options['id'];
        }
        if (isset($options['alias'])){
            $this->sqls_where .= " AND {$this->sqls_table}.alias = :{$this->sqls_table}_alias ";
            $this->bindings[":{$this->sqls_table}_alias"] = $options['alias'];
        }
        if (isset($options['slug'])){
            $this->sqls_where .= " AND {$this->sqls_table}.slug = :{$this->sqls_table}_slug ";
            $this->bindings[":{$this->sqls_table}_slug"] = $options['slug'];
        }

        $resultSql = "SELECT {$this->sqls_select} FROM {$this->sqls_from} WHERE {$this->sqls_where} {$this->sqls_order} LIMIT 0,1 ";
        if ($this->sqls_after) {
            $resultSql = StringModifier::replace($this->sqls_after, ['{#sql}' => $resultSql]);
            $this->sqls_after = '';
        }

        $dbHandler = $this->application->get($repository->getConfig()->dbServiceName);
        $ret = $dbHandler->selectOneObject($repository->getConfig()->modelClass, $resultSql, $this->bindings);

        if ($ret) {
            foreach ($this->sqls_connect as $inside){
                $this->resultInside($ret, 'inn_'.$inside.'_', $inside);
            }

            foreach($this->sqls_extend as $extend){
                if (isset($ret->$extend)){
                    $ret->$extend = $ret->$extend ? @json_decode($ret->$extend,true) : [];
                }
            }
        }
        return $ret;
    }

    protected function resultInside(&$someObject, $prefix, $subarray): void
    {
        $someObject->$subarray = [];
        foreach($someObject as $key => $val){
            if (strpos($key,$prefix) === 0){
                $someObject->$subarray[substr($key, strlen($prefix))] = $val;
                unset($someObject->$key);
            }
        }
    }
}
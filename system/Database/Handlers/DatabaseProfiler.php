<?php
namespace CodeHuiter\Database\Handlers;

use CodeHuiter\Config\Database\ConnectionDatabaseConfig;
use CodeHuiter\Service\Logger;

class DatabaseProfiler
{
    /**
     * @var Logger
     */
    private $log;

    private $benchmarkData = [];

    private $isLogIfLonger;

    private $isDebug;

    private $isLogTrace;

    public $isEnabled;

    /**
     * @var int
     */
    private $startTime = 0;

    /**
     * @var int
     */
    private $executeTime = 0;

    public function __construct(Logger $log, ConnectionDatabaseConfig $databaseConfig)
    {
        $this->log = $log;
        $this->isLogIfLonger = $databaseConfig->logIfLonger;
        $this->isDebug = $databaseConfig->debug;
        $this->isEnabled = ($this->isDebug === true) || ($this->isLogIfLonger !== false);
        $this->isLogTrace = $databaseConfig->logTrace;
    }

    public function preExecution(): void
    {
        if (!$this->isEnabled) {
            return;
        }
        $this->startTime = microtime(true);
    }

    public function preFormatting(): void
    {
        if (!$this->isEnabled) {
            return;
        }
        $this->executeTime = microtime(true);
    }

    public function done($query, $params): void
    {
        if (!$this->isEnabled) {
            return;
        }
        $finishTime = microtime(true);
        $executeTime = $this->executeTime - $this->startTime;
        $formattingTime = $finishTime - $this->executeTime;
        $totalTime = $executeTime + $formattingTime;

        if ($this->isDebug === true) {
            $this->benchmarkData[] = [
                'query' => $query,
                'params' => $params,
                'time_execute' => $executeTime,
                'time_format' => $formattingTime,
                'time_total' => $totalTime,
            ];
        }

        if ($this->isLogIfLonger !== false && ($totalTime > $this->isLogIfLonger / 1000)) {
            if ($this->isLogTrace === true) {
                $this->log->withTrace();
            }
            $this->log->withTag('DB_QUERY_TIME')->warning("Database query time is {$totalTime}", [
                'query' => $query,
                'params' => $params,
                'time_execute' => $executeTime,
                'time_format' => $formattingTime,
                'time_total' => $totalTime,
            ]);
        }
    }

    public function getBenchmarkData(): array
    {
        return $this->benchmarkData;
    }
}

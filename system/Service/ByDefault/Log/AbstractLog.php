<?php

namespace CodeHuiter\Service\ByDefault\Log;

use CodeHuiter\Config\LogConfig;
use CodeHuiter\Service\Logger;
use Exception;

abstract class AbstractLog implements Logger
{
    /**
     * @var LogConfig
     */
    protected $config;

    protected $levels = [
        'none'       => 0,
        'emergency'  => 1,
        'alert'      => 2,
        'critical'   => 3,
        'error'      => 4,
        'warning'    => 5,
        'notice'     => 6,
        'info'       => 7,
        'debug'      => 8,
    ];

    protected $enableLevels;

    protected $defaultLevel;

    protected $traceData;

    /** @var string */
    protected $tag = '';

    /**
     * @param LogConfig $config
     */
    public function __construct(LogConfig $config)
    {
        $this->config = $config;
        $this->defaultLevel = $this->config->defaultLevel ?? 'debug';

        $this->enableLevels = [];
        if (is_array($this->config->threshold)) {
            foreach($this->config->threshold as $levelKey) {
                $this->enableLevels[] = $this->levels[$levelKey];
            }
        } else {
            foreach($this->levels as $levelKey => $levelValue) {
                $this->enableLevels[] = $levelValue;
                if ($levelKey === $this->config->threshold) {
                    break;
                }
            }
        }
    }

    public function withTrace(): Logger
    {
        $this->traceData = [];
        $e = new Exception;
        $traceArray = explode("\n",$e->getTraceAsString());
        foreach($traceArray as $key => $traceArrayValue) {
            $this->traceData['#'.$key] = $traceArrayValue;
        }
        return $this;
    }

    public function withTag(string $tag = ''): Logger
    {
        $this->tag = $tag;
        return $this;
    }

    /**
     * @param string $message
     * @param array|null $context
     * @param string $level
     * @param string $tag
     * @return void
     */
    abstract public function log(string $message, ?array $context = null, string $level = '', string $tag = ''): void;

    /**
     * System is unusable.
     *
     * @param string $message
     * @param mixed $context
     */
    public function emergency(string $message, array $context = []): void
    {
        $this->log($message, $context, 'emergency', $this->tag);
        $this->clearAdditional();
    }

    //--------------------------------------------------------------------

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param mixed $context
     */
    public function alert(string $message, array $context = []): void
    {
        $this->log($message, $context, 'alert', $this->tag);
        $this->clearAdditional();
    }

    //--------------------------------------------------------------------

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param mixed $context
     */
    public function critical(string $message, array $context = []): void
    {
        $this->log($message, $context, 'critical', $this->tag);
        $this->clearAdditional();
    }

    //--------------------------------------------------------------------

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param mixed $context
     */
    public function error(string $message, array $context = []): void
    {
        $this->log($message, $context, 'error', $this->tag);
        $this->clearAdditional();
    }


    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param mixed $context
     */
    public function warning(string $message, array $context = []): void
    {
        $this->log($message, $context, 'warning', $this->tag);
        $this->clearAdditional();
    }

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param mixed $context
     */
    public function notice(string $message, array $context = []): void
    {
        $this->log($message, $context, 'notice', $this->tag);
        $this->clearAdditional();
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param mixed $context
     */
    public function info(string $message, array $context = []): void
    {
        $this->log($message, $context, 'info', $this->tag);
        $this->clearAdditional();
    }

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param mixed $context
     */
    public function debug(string $message, array $context = []): void
    {
        $this->log($message, $context, 'debug', $this->tag);
        $this->clearAdditional();
    }

    private function clearAdditional(): void
    {
        $this->tag = '';
        $this->traceData = [];
    }
}

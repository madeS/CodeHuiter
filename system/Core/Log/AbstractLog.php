<?php

namespace CodeHuiter\Core\Log;

use CodeHuiter\Core\Application;

abstract class AbstractLog
{
    const SERVICE_KEY = 'log';
    const CONFIG_KEY = 'log';

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

    /**
     * @param Application $application
     */
    public function __construct(Application $application)
    {
        $config = $application->getConfig(self::CONFIG_KEY);
        $this->config = $config;
        $this->defaultLevel = $config['default_level'] ?? 'debug';

        $this->enableLevels = [];
        if (is_array($config['threshold'])) {
            foreach($config['threshold'] as $levelKey) {
                $this->enableLevels[] = $this->levels[$levelKey];
            }
        } else {
            foreach($this->levels as $levelKey => $levelValue) {
                $this->enableLevels[] = $levelValue;
                if ($levelKey === $config['threshold']) {
                    break;
                }
            }
        }
    }

    public function addTrace()
    {
        $this->traceData = [];
        $e = new \Exception;
        $traceArray = explode("\n",$e->getTraceAsString());
        foreach($traceArray as $key => $traceArrayValue) {
            $this->traceData['#'.$key] = $traceArrayValue;
        }
    }

    abstract public function log($message, $context = null, $level = '', $tag = '');

    /** @var string $clog_out */
    protected $clog_out = '';

    /**
     * Лог в консоль
     * @param mixed $message Сообщение
     * @param bool $clearLine Стереть строку (если только она была набрана без завершения строки)
     * @param bool $endLine Завершить строку
     * @param string $level
     */
    public function clog($message, $clearLine = false, $endLine = true, $level = ''){
        if ($level === '') {
            $level = $this->defaultLevel;
        }
        if ($clearLine){
            print_r(str_pad('', strlen($this->clog_out), chr(0x08)));
            print_r(str_pad('', strlen($this->clog_out), ' '));
            print_r(str_pad('', strlen($this->clog_out), chr(0x08)));
            $this->clog_out = '';
        }
        if(is_object($message) || is_array($message)){
            $message = print_r($message,true);
        } else {
            $message = (string) $message;
        }
        $this->clog_out .= $message;
        print_r($message);
        if ($endLine){
            print_r("\r\n");
            $this->log($message, null, $level,'console');
            $this->clog_out = "";
        }
    }

    /**
     * System is unusable.
     *
     * @param string $message
     * @param mixed $context
     * @param string $tag
     */
    public function emergency($message, array $context = [], $tag = '')
    {
        $this->log($message, $context, 'emergency', $tag);
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
     * @param string $tag
     */
    public function alert($message, array $context = [], $tag = '')
    {
        $this->log($message, $context, 'alert', $tag);
    }

    //--------------------------------------------------------------------

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param mixed $context
     * @param string $tag
     */
    public function critical($message, array $context = [], $tag = '')
    {
        $this->log($message, $context, 'critical', $tag);
    }

    //--------------------------------------------------------------------

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param mixed $context
     * @param string $tag
     */
    public function error($message, array $context = [], $tag = '')
    {
        $this->log($message, $context, 'error', $tag);
    }


    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param mixed $context
     * @param string $tag
     */
    public function warning($message, array $context = [], $tag = '')
    {
        $this->log($message, $context, 'warning', $tag);
    }

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param mixed $context
     * @param string $tag
     */
    public function notice($message, array $context = [], $tag = '')
    {
        $this->log($message, $context, 'notice', $tag);
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param mixed $context
     * @param string $tag
     */
    public function info($message, array $context = [], $tag = '')
    {
        $this->log($message, $context, 'info', $tag);
    }

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param mixed $context
     * @param string $tag
     */
    public function debug($message, array $context = [], $tag = '')
    {
        $this->log($message, $context, 'debug', $tag);
    }
}

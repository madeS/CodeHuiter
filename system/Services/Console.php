<?php

namespace CodeHuiter\Services;

use CodeHuiter\Config\Config;
use CodeHuiter\Core\Application;
use CodeHuiter\Core\Log\AbstractLog;

class Console
{
    /**
     * @var AbstractLog|null
     */
    protected $log;

    /** @var string $clog_out */
    protected $clog_out = '';

    public function __construct(Application $application)
    {
        $this->log = $application->get(Config::SERVICE_KEY_LOG);
    }


    /**
     * Лог в консоль
     * @param mixed $message Сообщение
     * @param bool $clearLine Стереть строку (если только она была набрана без завершения строки)
     * @param bool $endLine Завершить строку
     */
    public function log($message, $clearLine = false, $endLine = true){
        if ($clearLine){
            print_r(str_pad('', mb_strlen($this->clog_out), chr(0x08)));
            print_r(str_pad('', mb_strlen($this->clog_out), ' '));
            print_r(str_pad('', mb_strlen($this->clog_out), chr(0x08)));
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
            $this->clog_out = "";
            if ($this->log) {
                $this->log->info($message, null, 'console');
            }
        }
    }


    protected $startTime = 0;
    public function progressRemaining($now,$total) {
        if ($this->startTime === 0 || $now == 0) {
            $this->startTime = microtime(true);
            return '???';
        }
        $nowTime = microtime(true);
        $done = $nowTime - $this->startTime;

        if ($now == 0) $now = 1;
        $totalTime = $done * $total / $now;
        $remainTime = $done * ($total - $now) / $now;
        $doneString = $this->timeSimpleToString($this->secondsToTimeSimple((int)$done));
        $totalString = $this->timeSimpleToString($this->secondsToTimeSimple((int)$totalTime));
        $remainString = $this->timeSimpleToString($this->secondsToTimeSimple((int)$remainTime));
        return "$doneString / $totalString | $remainString";
    }

    /** secondsToTimeSimple
     * Получение интервала в удобном виде количество дней, часов, минут, секунд
     * @param int $seconds
     * @return array $times:
     * $times[0] - секунды
     * $times[1] - минуты
     * $times[2] - часы
     * $times[3] - дни
     * $times[4] - года
     */
    protected function secondsToTimeSimple($seconds){
        if ($seconds < 0){ $seconds = - $seconds; }
        $times = array(0,0,0,0,0);
        $periods = array(60, 3600, 86400, 31536000);
        for ($i = 3; $i >= 0; $i--){
            $period = floor($seconds/$periods[$i]);
            if (($period > 0)) {
                $times[$i+1] = $period;
                $seconds -= $period * $periods[$i];
            }
        }
        $times[0] = $seconds;

        return $times;
    }

    protected function timeSimpleToString($r)
    {
        return trim(''
            . (($r[4])? $r[4].'Y' : '')
            . (($r[3])? ' ' . $r[3].'M' : '')
            . (($r[2])? ' ' . $r[2].'H' : '')
            . (($r[1])? ' ' . $r[1].'m' : '')
            . (($r[0])? ' ' . $r[0].'s' : ''));
    }
}

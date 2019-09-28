<?php

namespace CodeHuiter\Service\ByDefault;

use CodeHuiter\Service\Logger;

class Console implements \CodeHuiter\Service\Console
{
    /**
     * @var Logger|null
     */
    protected $log;

    /** @var string $clog_out */
    protected $clog_out = '';

    public function __construct(?Logger $log = null)
    {
        $this->log = $log;
    }

    /**
     * @param mixed $message
     * @param bool $clearLine
     * @param bool $endLine
     */
    public function log($message, $clearLine = false, $endLine = true): void
    {
        if ($clearLine){
            print(str_pad('', mb_strlen($this->clog_out), chr(0x08)));
            print(str_pad('', mb_strlen($this->clog_out), ' '));
            print(str_pad('', mb_strlen($this->clog_out), chr(0x08)));
            $this->clog_out = '';
        }
        if(is_object($message) || is_array($message)){
            $message = print_r($message,true);
        } else {
            $message = (string) $message;
        }
        $this->clog_out .= $message;
        print($message);
        if ($endLine){
            print("\r\n");
            $this->clog_out = '';
            if ($this->log) {
                $this->log->withTag('console')->info($message);
            }
        }
    }


    protected $startTime = 0;

    /**
     * @param int $now
     * @param int $total
     * @return string
     */
    public function progressRemaining(int $now, int $total): string
    {
        if ($this->startTime === 0 || $now === 0) {
            $this->startTime = microtime(true);
            return '???';
        }
        $nowTime = microtime(true);
        $done = $nowTime - $this->startTime;

        if ($now === 0) {
            $now = 1;
        }
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
    protected function secondsToTimeSimple($seconds): array
    {
        if ($seconds < 0) {
            $seconds = - $seconds;
        }
        $times = array(0,0,0,0,0);
        $periods = array(60, 3600, 86400, 31536000);
        for ($i = 3; $i >= 0; $i--){
            $period = floor($seconds/$periods[$i]);
            if ($period > 0) {
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
            . ($r[4] ? $r[4].'Y' : '')
            . ($r[3]? ' ' . $r[3].'M' : '')
            . ($r[2]? ' ' . $r[2].'H' : '')
            . ($r[1]? ' ' . $r[1].'m' : '')
            . ($r[0]? ' ' . $r[0].'s' : '')
        );
    }
}

<?php

namespace CodeHuiter\Services;

use CodeHuiter\Config\Config;
use CodeHuiter\Core\Application;
use CodeHuiter\Pattern\Modules\Auth\Models\UsersModel;

class DateService
{
    /** @var int  */
    public $now = 0;

    /**
     * @var array
     */
    protected $config;

    protected $stateTime = 0;
    protected $outTimezone = null;
    protected $utcAppend = null;

    public function __construct(Application $application)
    {
        $this->config = $application->getConfig(Config::CONFIG_KEY_DATE);
        $this->now = time();
    }

    public function fromTime($timestamp = null)
    {
        $this->stateTime = ($timestamp === null) ? $this->now : $timestamp;
        $this->outTimezone = null;
        $this->utcAppend = null;
        return $this;
    }

    public function fromString($string, $timezone = 'UTC')
    {
        date_default_timezone_set($timezone);
        $this->stateTime = strtotime($string);
        $this->outTimezone = null;
        $this->utcAppend = null;
        return $this;
    }

    public function forTimezone($timezone)
    {
        if ($timezone) {
            $this->outTimezone = $timezone;
        }
        return $this;
    }

    public function forUser(UsersModel $user = null)
    {
        if ($user && $user->timezone !== '') {
            $this->outTimezone = intval($user->timezone) * 60;
        }
        return $this;
    }

    public function toTime()
    {
        return $this->stateTime;
    }

    public function toFormat($format, $isFormat = false, $utcAppend = false)
    {
        $append = '';
        if ($this->outTimezone === null) {
            $this->outTimezone = $this->config['site_timezone'];
        }

        if (is_int($this->outTimezone)) {
            date_default_timezone_set('UTC');
            $this->stateTime -= intval($this->outTimezone) * 60;
            if ($utcAppend) {
                $times = $this->secondsToTimeSimple(- $this->outTimezone * 60);
                $append = ' UTC';
                $append .= (($times[2]>0) ? '+' . $times[2] : '-' . abs($times[2]));
                $append .= (($times[1])?':'.abs($times[1]):'');
            }
        } elseif (is_string($this->outTimezone) && $this->outTimezone) {
            date_default_timezone_set($this->outTimezone);
        } else {
            date_default_timezone_set('UTC');
        }

        if ($isFormat) {
            return strftime($format, $this->stateTime);
        }
        return date($format, $this->stateTime);
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
        if ($seconds < 0) { $seconds = - $seconds; }
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


    public function sqlTime($time){
        return $this->fromTime($time)->forTimezone('UTC')->toFormat('Y-m-d H:i:s');
    }

}

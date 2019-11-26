<?php

namespace CodeHuiter\Service\ByDefault;

use CodeHuiter\Config\DateConfig;
use CodeHuiter\Core\Application;
use CodeHuiter\Exception\Runtime\DateTimeConvertException;
use CodeHuiter\Pattern\Module\Auth\Model\UserInterface;
use DateInterval;
use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Exception;

class DateService implements \CodeHuiter\Service\DateService
{
    private const UTC_TIMEZONE = 'UTC';
    private const STRING_FORMAT = 'Y-m-d H:i:s';

    /** @var int */
    public $now = 0;

    /** @var Application */
    public $app;

    /** @var DateConfig */
    protected $config;

    /** @var int */
    protected $stateTime = 0;

    /** @var int|string|null */
    protected $outTimezone;

    /** @var bool */
    protected $utcAppend = false;

    /**
     * @param DateConfig $dateConfig
     */
    public function __construct(DateConfig $dateConfig)
    {
        $this->config = $dateConfig;
        $this->now = time();
    }

    /**
     * @return int
     */
    public function getCurrentTimestamp(): int
    {
        return $this->now;
    }

    /**
     * @param int|null $timestamp
     * @return \CodeHuiter\Service\DateService
     */
    public function fromTime(?int $timestamp = null): \CodeHuiter\Service\DateService
    {
        $this->stateTime = $timestamp === null ? $this->now : $timestamp;
        $this->outTimezone = null;
        $this->utcAppend = false;
        return $this;
    }

    /**
     * @param string $string
     * @param string $timezone
     * @return \CodeHuiter\Service\DateService
     */
    public function fromString(string $string, string $timezone = 'UTC'): \CodeHuiter\Service\DateService
    {
        date_default_timezone_set($timezone);
        $this->stateTime = strtotime($string);
        $this->outTimezone = null;
        $this->utcAppend = false;
        return $this;
    }

    /**
     * @param string $timezone
     * @return \CodeHuiter\Service\DateService
     */
    public function forTimezone(string $timezone): \CodeHuiter\Service\DateService
    {
        if ($timezone) {
            $this->outTimezone = $timezone;
        }
        return $this;
    }

    /**
     * @param UserInterface|null $user
     * @return \CodeHuiter\Service\DateService
     */
    public function forUser(UserInterface $user = null): \CodeHuiter\Service\DateService
    {
        if ($user && $user->getTimezone() !== '') {
            $this->outTimezone = (int)$user->getTimezone();
        }
        return $this;
    }

    /**
     * @return int
     */
    public function toTime(): int
    {
        return $this->stateTime;
    }

    /**
     * @param string $format
     * @param bool $isFormat
     * @param bool $utcAppend
     * @return string
     */
    public function toFormat(string $format, bool $isFormat = false, bool $utcAppend = false): string
    {
        $append = '';
        if ($this->outTimezone === null) {
            $this->outTimezone = $this->config->siteTimezone;
        }

        if (is_int($this->outTimezone)) {
            date_default_timezone_set('UTC');
            $this->stateTime -= (int)$this->outTimezone * 60;
            if ($utcAppend) {
                $times = $this->secondsToTimeSimple(- $this->outTimezone * 60);
                $append = ' UTC';
                $append .= (($times[2]>0) ? '+' . $times[2] : '-' . abs($times[2]));
                $append .= ($times[1] ? ':' . abs($times[1]) : '');
            }
        } elseif (is_string($this->outTimezone) && $this->outTimezone) {
            date_default_timezone_set($this->outTimezone);
        } else {
            date_default_timezone_set('UTC');
        }

        if ($isFormat) {
            return strftime($format, $this->stateTime) . $append;
        }
        return date($format, $this->stateTime) . $append;
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
        if ($seconds < 0) { $seconds = - $seconds; }
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

    /**
     * @param int|null $time
     * @return string
     */
    public function sqlTime(?int $time = null): string
    {
        return $this->fromTime($time)->forTimezone('UTC')->toFormat('Y-m-d H:i:s');
    }

    /**
     * Modify timestamp for days
     * @param int $timeStamp
     * @param $days
     * @return int
     * @throws Exception
     */
    public function addDays(int $timeStamp, int $days): int
    {
        return (new DateTime())->setTimestamp($timeStamp)->add(new DateInterval("P{$days}D"))->getTimestamp();
    }

    public function diffDateTime(string $stringMin, ?string $stringMax = null): DateInterval
    {
        $dateTimeMax = ($stringMax !== null) ? $this->timeStringToDateTime($stringMax) : $this->getCurrentDateTime();
        $dateTimeMin = $this->timeStringToDateTime($stringMin);
        return $dateTimeMin->diff($dateTimeMax);
    }

    public function getCurrentDateTime(): DateTimeImmutable
    {
        try {
            return (new DateTimeImmutable())->setTimezone($this->getUTCTimeZone());
        } catch (Exception $exception) {
            throw DateTimeConvertException::cantCreateCurrentDateTime($exception);
        }
    }

    public function addSeconds(DateTimeImmutable $time, int $seconds): DateTimeImmutable
    {
        try {
            return $time->add(new DateInterval('PT' . $seconds . 'S'));
        } catch (Exception $exception) {
            throw DateTimeConvertException::cantCreateDateInterval('PT' . $seconds . 'S', $exception);
        }
    }

    public function subSeconds(DateTimeImmutable $time, int $seconds): DateTimeImmutable
    {
        try {
            return $time->sub(new DateInterval('PT' . $seconds . 'S'));
        } catch (Exception $exception) {
            throw DateTimeConvertException::cantCreateDateInterval('PT' . $seconds . 'S', $exception);
        }
    }

    public function timeStringToDateTime(string $string): DateTimeImmutable
    {
        if ($string && strpos($string, ' ') === false) {
            $string .= ' 00:00:00';
        }
        $result = DateTimeImmutable::createFromFormat(self::STRING_FORMAT, $string, $this->getUTCTimeZone());
        if ($result === false) {
            throw DateTimeConvertException::cantConvertStringToDateTime($string);
        }
        return $result;
    }

    public function dateTimeToTimeString(DateTimeImmutable $datetime): string
    {
        return $datetime->format(self::STRING_FORMAT);
    }

    public function getCurrentTimeAsString(): string
    {
        return $this->getCurrentDateTime()->format(self::STRING_FORMAT);
    }

    private function getUTCTimeZone(): DateTimeZone
    {
        return new DateTimeZone(self::UTC_TIMEZONE);
    }
}

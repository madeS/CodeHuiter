<?php

namespace CodeHuiter\Service;

use CodeHuiter\Pattern\Module\Auth\Model\UserInterface;

interface DateService
{
    /**
     * @param int|null $timestamp
     * @return DateService
     */
    public function fromTime(?int $timestamp = null): DateService;

    /**
     * Create DateService State for time from string
     *
     * @param string $string
     * @param string $timezone
     * @return DateService
     */
    public function fromString(string $string, string $timezone = 'UTC'): DateService;

    /**
     * Prepare time for format for timezone
     *
     * @param string $timezone
     * @return DateService
     */
    public function forTimezone(string $timezone): DateService;

    /**
     * Prepare time for format for user using his timezone
     *
     * @param UserInterface|null $user
     * @return DateService
     */
    public function forUser(UserInterface $user = null): DateService;

    /**
     * Format time to timestamp
     *
     * @return int
     */
    public function toTime(): int;

    /**
     * Format Time to string
     *
     * @param string $format
     * @param bool $isFormat
     * @param bool $utcAppend
     * @return string
     */
    public function toFormat(string $format, bool $isFormat = false, bool $utcAppend = false): string;

    /**
     * Date in Y-m-d H:i:s format for SQL DateTime
     *
     * @param int|null $time
     * @return string
     */
    public function sqlTime(?int $time = null): string;

    /**
     * Modify timestamp for days
     * @param int $timeStamp
     * @param int $days
     * @return int
     */
    public function addDays(int $timeStamp, int $days): int;
}

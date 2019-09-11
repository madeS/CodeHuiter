<?php

namespace CodeHuiter\Service;

interface Logger
{
    /**
     * @return Logger
     */
    public function withTrace(): Logger;

    /**
     * @param string $tag
     * @return Logger
     */
    public function withTag(string $tag = ''): Logger;

    /**
     * System is unusable.
     *
     * @param string $message
     * @param array $context
     */
    public function emergency(string $message, array $context = []): void;

    //--------------------------------------------------------------------

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param array $context
     */
    public function alert(string $message, array $context = []): void;

    //--------------------------------------------------------------------

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param array $context
     */
    public function critical(string $message, array $context = []): void;

    //--------------------------------------------------------------------

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array $context
     */
    public function error(string $message, array $context = []): void;


    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param array $context
     */
    public function warning(string $message, array $context = []): void;

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array $context
     */
    public function notice(string $message, array $context = []): void;

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array $context
     */
    public function info(string $message, array $context = []): void;

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param mixed $context
     */
    public function debug(string $message, array $context = []): void;
}

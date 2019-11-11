<?php

namespace CodeHuiter\Service\ByDefault\Email;

use CodeHuiter\Config\EmailConfig;
use CodeHuiter\Service\Logger;

abstract class AbstractEmail implements \CodeHuiter\Service\Mailer
{
    /**
     * @var Logger
     */
    protected $log;

    /**
     * @var EmailConfig
     */
    protected $config;

    /**
     * @var string
     */
    protected $lastStatusMessage = '';

    /**
     * @param EmailConfig $config
     * @param Logger $log
     */
    public function __construct(EmailConfig $config, Logger $log)
    {
        $this->log = $log;
        $this->config = $config;
    }

    /**
     * @return string
     */
    public function getLastStatusMessage(): string
    {
        return $this->lastStatusMessage;
    }

    /**
     * @param string $subject
     * @param string $content
     * @param array $emails [email1, email2, email3]
     * @param array $ccEmails [email1, email2, email3]
     * @param bool $queued
     * @param bool $force
     * @return bool
     */
    public function sendFromSite(
        string $subject,
        string $content,
        array $emails,
        array $ccEmails = [],
        bool $queued = true,
        bool $force = false
    ): bool {
        $from = [$this->config->siteRobotEmail => $this->config->siteRobotName];
        return $this->send($subject, $content, $from, $emails, $ccEmails, $queued, $force);
    }

    /**
     * @param string $subject
     * @param string $content
     * @param array $from [email => name]
     * @param array $emails [email1, email2, email3]
     * @param array $ccEmails [email1, email2, email3]
     * @param bool $queued
     * @param bool $force
     * @return bool
     */
    abstract public function send(
        string $subject,
        string $content,
        array $from,
        array $emails,
        array $ccEmails,
        bool $queued,
        bool $force
    ): bool;
}
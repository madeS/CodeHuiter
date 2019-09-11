<?php

namespace CodeHuiter\Service\Email;

use CodeHuiter\Config\Config;
use CodeHuiter\Config\EmailConfig;
use CodeHuiter\Core\Application;
use CodeHuiter\Service\Logger;

abstract class AbstractEmail
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

    public function __construct(EmailConfig $config, Logger $log)
    {
        $this->log = $log;
        $this->config = $config;
    }

    public function getLastStatusMessage()
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
    public function sendFromSite($subject, $content, $emails, $ccEmails = [], $queued = true, bool $force = false)
    {
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
    abstract public function send($subject, $content, $from, $emails, $ccEmails, $queued, bool $force);
}
<?php

namespace CodeHuiter\Services\Email;


use CodeHuiter\Core\Application;
use CodeHuiter\Core\Log\AbstractLog;

abstract class AbstractEmail
{
    const CONFIG_KEY = 'email';

    /**
     * @var AbstractLog
     */
    protected $log;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var string
     */
    protected $lastStatusMessage = '';

    public function __construct(Application $application)
    {
        $this->log = $application->get(AbstractLog::SERVICE_KEY);
        $this->config = $application->getConfig(self::CONFIG_KEY);
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
     * @return bool
     */
    public function sendFromSite($subject, $content, $emails, $ccEmails = [], $queued = true)
    {
        $from = [$this->config['site_robot_email'] => $this->config['site_robot_name']];
        return $this->send($subject, $content, $from, $emails, $ccEmails, $queued);
    }

    /**
     * @param string $subject
     * @param string $content
     * @param array $from [email => name]
     * @param array $emails [email1, email2, email3]
     * @param array $ccEmails [email1, email2, email3]
     * @param bool $queued
     * @return bool
     */
    abstract public function send($subject, $content, $from, $emails, $ccEmails, $queued);
}
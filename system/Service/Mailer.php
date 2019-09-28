<?php

namespace CodeHuiter\Service;

interface Mailer
{
    /**
     * @return string
     */
    public function getLastStatusMessage(): string;

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
        $subject,
        $content,
        $emails,
        $ccEmails = [],
        $queued = true,
        bool $force = false
    ): bool;

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
    public function send(
        $subject,
        $content,
        $from,
        $emails,
        $ccEmails,
        $queued,
        bool $force
    ): bool;
}

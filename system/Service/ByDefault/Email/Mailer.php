<?php

namespace CodeHuiter\Service\ByDefault\Email;

use CodeHuiter\Config\EmailConfig;
use CodeHuiter\Service\ByDefault\Email\Model\MailerModel;
use CodeHuiter\Service\ByDefault\Email\Sender\EmailSender;
use CodeHuiter\Service\Logger;
use CodeHuiter\Exception\TagException;
use CodeHuiter\Service\DateService;

class Mailer extends AbstractEmail implements \CodeHuiter\Service\Mailer
{
    /**
     * @var EmailSender
     */
    protected $emailSender = null;

    /**
     * @var DateService
     */
    protected $date;

    /**
     * @param EmailConfig $config
     * @param Logger $log
     * @param DateService $dateService
     */
    public function __construct(EmailConfig $config, Logger $log, DateService $dateService)
    {
        $this->date = $dateService;
        parent::__construct($config, $log);
    }

    /**
     * @throws TagException
     * @return EmailSender
     */
    protected function getEmailSender(): string
    {
        if ($this->emailSender === null) {
            $this->emailSender = new EmailSender($this->config->senderConfig);
        }
        return $this->emailSender;
    }

    /**
     * @inheritdoc
     */
    public function send($subject, $content, $from, $emails, $ccEmails, $queued, bool $force): bool
    {
        if ($queued) {
            foreach ($emails as $email) {
                $mailerData = [
                    'user_id' => 0,
                    'subject' => $subject,
                    'email' => $email,
                    'message' => $content,
                    'created_at'=> $this->date->fromTime()->toTime(),
                    'updated_at' => $this->date->fromTime()->toTime()(),
                    'sended' => 0,
                ];
                $mailerId = MailerModel::insert($mailerData);
                if ($this->config->queueForce || $force) {
                    return $this->sendFromQueue(1, $mailerId);
                }
            }
            return true;
        }

        return $this->innerSend($subject, $content, $from, $emails, $ccEmails);
    }

    /**
     * @param string $subject
     * @param string $content
     * @param array $from [email => name]
     * @param array $emails [email1, email2, email3]
     * @param array $ccEmails [email1, email2, email3]
     * @return bool
     */
    protected function innerSend($subject, $content, $from, $emails, $ccEmails): bool
    {
        $success = false;
        $this->lastStatusMessage = '';
        try {
            $sender = $this->getEmailSender();
            if (strpos($content,'<') === 0) {
                $sender->setMailType('html');
            } else {
                $sender->setMailType('text');
            }
            $sender->setSubject($subject);
            $sender->setMessage($content);
            foreach ($from as $email => $name) {
                $sender->setFrom($email, $name);
            }
            $sender->setTo($emails);
            if ($ccEmails) {
                $sender->setCC($ccEmails);
            }
            $success = $sender->send();
        } catch (TagException $exception) {
            $this->lastStatusMessage .= $exception->getMessage();
        }
        $this->lastStatusMessage .= isset($sender) ? $sender->printDebugger() : '';
        $toEmailsString = '[' . implode(',', $emails) . ']';
        if (!$success) {
            $this->log->withTag('MAILER')->warning(
                "Email to $toEmailsString Subject: $subject not sent. " . $this->lastStatusMessage,
                []
            );
            return false;
        }
        $this->log->withTag('MAILER')->info(
            "Sent email to $toEmailsString Subject: $subject",
            []
        );
        return true;
    }

    /**
     * @param int $count
     * @param int|null $id
     * @return bool
     */
    protected function sendFromQueue($count = 1, $id = null): bool
    {
        /** @var MailerModel[] $messages */
        $messages = MailerModel::getWhere(
            ($id ? ['id' => $id] : []),
            [
                'order' => ['field' => 'updated_at'],
                'limit' => ['count' => $count],
            ]
        );
        $success = false;
        foreach($messages as $message){
            $success = $this->sendFromSite(
                $message->subject,
                $message->message,
                [$message->email],
                [],
                false
            );
            if ($success){
                $message->update([
                    'sended' => 1,
                    'updated_at' => $this->date->fromTime()->toTime(),
                ]);
            } else {
                $message->update([
                    'updated_at' => $this->date->fromTime()->toTime(),
                ]);
            }
        }
        return $success;
    }
}

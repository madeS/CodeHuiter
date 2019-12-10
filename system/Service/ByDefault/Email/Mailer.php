<?php

namespace CodeHuiter\Service\ByDefault\Email;

use CodeHuiter\Config\EmailConfig;
use CodeHuiter\Core\Application;
use CodeHuiter\Database\RelationalModelRepository;
use CodeHuiter\Service\ByDefault\Email\Model\MailerModel;
use CodeHuiter\Service\ByDefault\Email\Sender\EmailSender;
use CodeHuiter\Service\Logger;
use CodeHuiter\Exception\TagException;
use CodeHuiter\Service\DateService;

class Mailer extends AbstractEmail
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
     * @var RelationalModelRepository
     */
    protected $mailerRepository;

    /**
     * @param EmailConfig $config
     * @param Logger $log
     * @param DateService $dateService
     */
    public function __construct(EmailConfig $config, Logger $log, DateService $dateService)
    {
        $this->date = $dateService;
        $this->mailerRepository = new RelationalModelRepository(Application::getInstance(), new MailerModel());
        parent::__construct($config, $log);
    }

    /**
     * @throws TagException
     * @return EmailSender
     */
    protected function getEmailSender(): EmailSender
    {
        if ($this->emailSender === null) {
            $this->emailSender = new EmailSender($this->config->senderConfig);
        }
        return $this->emailSender;
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
    public function send(
        string $subject,
        string $content,
        array $from,
        array $emails,
        array $ccEmails,
        bool $queued,
        bool $force
    ): bool {
        if ($queued) {
            foreach ($emails as $email) {
                /** @var MailerModel $model */
                $model = MailerModel::getEmpty();
                // TODO rewrite by sets ?
                $model->updateBySet([
                    'user_id' => 0,
                    'subject' => $subject,
                    'email' => $email,
                    'message' => $content,
                    'sended' => 0,
                ]);
                $this->mailerRepository->save($model);
                if ($this->config->queueForce || $force) {
                    return $this->sendFromQueue(1, $model->id);
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
        $messages = $this->mailerRepository->find(
            $id ? ['id' => $id] : [],
            [
                'order' => ['updated_at' => 'asc'],
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
                $message->sended = 1;
            }
            $message->updated_at = $this->date->fromTime()->toTime();
            $this->mailerRepository->save($message);
        }
        return $success;
    }
}

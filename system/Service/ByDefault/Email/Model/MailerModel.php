<?php

namespace CodeHuiter\Service\ByDefault\Email\Model;

use CodeHuiter\Database\Model;

class MailerModel extends Model
{
    /** @var int|null */
    public $id;
    /** @var int */
    public $user_id;
    /** @var string */
    public $subject;
    /** @var string */
    public $email;
    /** @var string */
    public $message;
    /** @var string */
    public $created_at;
    /** @var string */
    public $updated_at;
    /** @var bool */
    public $sended;
}
<?php

namespace CodeHuiter\Service\ByDefault\Email\Model;

use CodeHuiter\Database\RelationalModel;

class MailerModel extends RelationalModel
{
    protected $_table = 'mailer';
    protected $_databaseServiceKey = 'db';
    protected $_primaryFields = ['id'];
    protected $_autoIncrementField = 'id';

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
<?php

namespace CodeHuiter\Service\ByDefault\Email\Model;

use CodeHuiter\Database\Model;

class MailerModel extends Model
{
    protected static $database = 'db'; // database_default config
    protected static $table = 'mailer';
    protected static $primaryFields = ['id'];
    protected static $fields = [
        'id',
        'user_id',
        'subject',
        'email',
        'message',
        'created_at',
        'updated_at',
        'sended',
    ];

    public $id;
    public $user_id;
    public $subject;
    public $email;
    public $message;
    public $created_at;
    public $updated_at;
    public $sended;

}
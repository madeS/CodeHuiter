<?php

namespace CodeHuiter\Pattern\Module\Auth\Model;

use CodeHuiter\Database\Model;

class UsersGroupsModel extends Model
{
    protected static $database = 'db'; // database_default config
    protected static $table = 'users_groups';
    protected static $primaryKeys = ['user_id', 'group_id'];
    protected static $fields = [
        'user_id',
        'group_id',
        'created_at',
    ];

    public $user_id;
    public $group_id;
    public $created_at;
}

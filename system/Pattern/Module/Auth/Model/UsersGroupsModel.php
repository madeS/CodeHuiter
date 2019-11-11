<?php

namespace CodeHuiter\Pattern\Module\Auth\Model;

use CodeHuiter\Database\RelationalModel;

class UsersGroupsModel extends RelationalModel
{
    protected $_table = 'users_groups';
    protected $_databaseServiceKey = 'db';
    protected $_primaryFields = ['user_id', 'group_id'];
    protected $_autoIncrementField = '';

    public $user_id;
    public $group_id;
    public $created_at;
}

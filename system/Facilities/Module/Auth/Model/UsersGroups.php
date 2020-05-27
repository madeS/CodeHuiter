<?php

namespace CodeHuiter\Facilities\Module\Auth\Model;

use CodeHuiter\Database\Model;

class UsersGroups extends Model
{
    public $user_id;
    public $group_id;
    public $created_at;
}

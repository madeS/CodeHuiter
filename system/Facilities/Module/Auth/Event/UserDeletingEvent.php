<?php

namespace CodeHuiter\Facilities\Module\Auth\Event;

use CodeHuiter\Config\AuthConfig;
use CodeHuiter\Facilities\Module\Auth\Model\User;
use CodeHuiter\Service\EventDispatcher\Event;

class UserDeletingEvent implements Event
{
    /** @var User */
    public $userInfo;

    public function __construct(User $userInfo)
    {
        $this->userInfo = $userInfo;
    }

    public function getEventName(): string
    {
        return AuthConfig::EVENT_USER_DELETING;
    }
}

<?php

namespace CodeHuiter\Facilities\Module\Auth\Event;

use CodeHuiter\Config\Facilities\Module\AuthConfig;
use CodeHuiter\Facilities\Module\Auth\Model\User;
use CodeHuiter\Service\EventDispatcher\Event;

class JoinAccountsEvent implements Event
{
    /** @var User */
    public $donorUser;

    /** @var User */
    public $targetUser;

    /**
     * @param User $donorUser
     * @param User $targetUser
     */
    public function __construct(User $donorUser, User $targetUser)
    {
        $this->donorUser = $donorUser;
        $this->targetUser = $targetUser;
    }

    public function getEventName(): string
    {
        return AuthConfig::EVENT_USER_JOIN_ACCOUNT;
    }
}

<?php

namespace CodeHuiter\Pattern\Module\Auth\Event;

use CodeHuiter\Config\AuthConfig;
use CodeHuiter\Pattern\Module\Auth\Model\UserInterface;
use CodeHuiter\Service\EventDispatcher\Event;

class JoinAccountsEvent implements Event
{
    /** @var UserInterface */
    public $donorUser;

    /** @var UserInterface */
    public $targetUser;

    /**
     * @param UserInterface $donorUser
     * @param UserInterface $targetUser
     */
    public function __construct(UserInterface $donorUser, UserInterface $targetUser)
    {
        $this->donorUser = $donorUser;
        $this->targetUser = $targetUser;
    }

    public function getEventName(): string
    {
        return AuthConfig::EVENT_USER_JOIN_ACCOUNT;
    }
}

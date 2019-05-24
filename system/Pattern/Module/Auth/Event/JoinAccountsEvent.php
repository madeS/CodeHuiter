<?php

namespace CodeHuiter\Pattern\Module\Auth\Event;

use CodeHuiter\Core\Event\ApplicationEvent;
use CodeHuiter\Pattern\Module\Auth\Model\UserInterface;

class JoinAccountsEvent implements ApplicationEvent
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
}

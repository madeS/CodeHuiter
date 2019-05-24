<?php

namespace CodeHuiter\Pattern\Module\Auth\Event;

use CodeHuiter\Core\Event\ApplicationEvent;
use CodeHuiter\Pattern\Module\Auth\Model\UserInterface;

class UserDeletingEvent implements ApplicationEvent
{
    /** @var UserInterface */
    public $userInfo;

    /**
     * @param UserInterface $userInfo
     * @param int[] $previousGroups
     */
    public function __construct(UserInterface $userInfo)
    {
        $this->userInfo = $userInfo;
    }
}

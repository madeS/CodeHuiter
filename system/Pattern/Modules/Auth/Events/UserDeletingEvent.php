<?php

namespace CodeHuiter\Pattern\Modules\Auth\Events;

use CodeHuiter\Core\Event\ApplicationEvent;
use CodeHuiter\Pattern\Modules\Auth\Models\UserInterface;

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

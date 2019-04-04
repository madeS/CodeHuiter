<?php

namespace CodeHuiter\Pattern\Modules\Auth\Events;

use CodeHuiter\Core\Event\ApplicationEvent;
use CodeHuiter\Modifiers\ArrayModifier;
use CodeHuiter\Pattern\Modules\Auth\Models\UserInterface;

class GroupsChangedEvent implements ApplicationEvent
{
    /** @var UserInterface */
    public $userInfo;

    /** @var int[] */
    public $addedGroups;

    /** @var int[] */
    public $removedGroups;

    /**
     * @param UserInterface $userInfo
     * @param int[] $previousGroups
     */
    public function __construct(UserInterface $userInfo, array $previousGroups)
    {
        $this->userInfo = $userInfo;
        $diff = ArrayModifier::diff($previousGroups, $userInfo->getGroups());
        $this->addedGroups = $diff['added'];
        $this->removedGroups = $diff['removed'];
    }
}

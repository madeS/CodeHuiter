<?php

namespace CodeHuiter\Pattern\Module\Auth\Event;

use CodeHuiter\Config\AuthConfig;
use CodeHuiter\Modifier\ArrayModifier;
use CodeHuiter\Pattern\Module\Auth\Model\UserInterface;
use CodeHuiter\Service\EventDispatcher\Event;

class GroupsChangedEvent implements Event
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

    public function getEventName(): string
    {
        return AuthConfig::EVENT_USER_GROUP_CHANGED;
    }
}

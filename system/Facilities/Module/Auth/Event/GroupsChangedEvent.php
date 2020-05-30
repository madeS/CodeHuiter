<?php

namespace CodeHuiter\Facilities\Module\Auth\Event;

use CodeHuiter\Config\Facilities\Module\AuthConfig;
use CodeHuiter\Modifier\ArrayModifier;
use CodeHuiter\Facilities\Module\Auth\Model\User;
use CodeHuiter\Service\EventDispatcher\Event;

class GroupsChangedEvent implements Event
{
    /** @var User */
    public $userInfo;

    /** @var int[] */
    public $addedGroups;

    /** @var int[] */
    public $removedGroups;

    /**
     * @param User $userInfo
     * @param int[] $previousGroups
     */
    public function __construct(User $userInfo, array $previousGroups)
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

<?php

namespace CodeHuiter\Pattern\Module\Media\Event;

use CodeHuiter\Config\AuthConfig;
use CodeHuiter\Core\Application;
use CodeHuiter\Pattern\Module\Auth\Event\JoinAccountsEvent;
use CodeHuiter\Pattern\Module\Media\Model\MediaModel;
use CodeHuiter\Pattern\Module\Media\Model\MediaRepository;
use CodeHuiter\Service\EventDispatcher\Event;
use CodeHuiter\Service\EventDispatcher\EventSubscriber;

class MediaSubscriber implements EventSubscriber
{
    /**
     * @var Application
     */
    private $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    public function catchEvent(Event $event): void
    {
        if ($event->getEventName() === AuthConfig::EVENT_USER_JOIN_ACCOUNT && $event instanceof JoinAccountsEvent) {
            $this->joinMedias($event->donorUser->getId(), $event->targetUser->getId());
        }
    }

    private function joinMedias(string $donorUserId, string $targetUserId): void
    {
        $this->getMediaRepository()->update(
            [MediaModel::FIELD_USER_ID => $donorUserId],
            [MediaModel::FIELD_USER_ID => $targetUserId]
        );
    }

    private function getMediaRepository(): MediaRepository
    {
        return $this->application->get(MediaRepository::class);
    }
}
<?php

namespace CodeHuiter\Facilities\Module\Connector;

use CodeHuiter\Config\Facilities\Module\ConnectorConfig;
use CodeHuiter\Core\Application;
use CodeHuiter\Facilities\Module\Auth\Model\User;
use CodeHuiter\Facilities\Module\Auth\UserService;

/**
 * Extend this class for configure accessibility
 */
class ConnectAccessibility
{
    /** @var Application */
    private $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    public function canAddTo(string $objectTypeThat, ConnectableObject $objectTo, User $user): ?bool
    {
        if ($objectTypeThat === ConnectorConfig::TYPE_PHOTO) {
            if ($objectTo->getConnectorType() === ConnectorConfig::TYPE_PROFILE) {
                if ($objectTo->getConnectorTypedId() === $user->getId() || $this->getUserService()->isModerator($user)) {
                    return true;
                }
                return false;
            }
        }
        return null;
    }

    private function getUserService(): UserService
    {
        return $this->application->get(UserService::class);
    }
}
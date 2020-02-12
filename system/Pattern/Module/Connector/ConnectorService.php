<?php

namespace CodeHuiter\Pattern\Module\Connector;

use CodeHuiter\Config\ConnectorConfig;
use CodeHuiter\Core\Application;

class ConnectorService
{
    /** @var Application */
    private $application;
    /** @var ConnectorConfig */
    private $config;

    public function __construct(
        Application $application,
        ConnectorConfig $config
    ) {
        $this->application = $application;
        $this->config = $config;
    }

    public function getConnectableObjectByIdentity(string $identity): ?ConnectableObject
    {
        $type = self::getTypeFromIdentity($identity);
        $typedId = self::getTypedIdFromIdentity($identity);
        if ($type === ConnectorConfig::TYPE_TEMP) {
            return new TempConnectableObject();
        }
        /** @var ConnectableObjectRepository $repository */
        $repository = $this->application->get($this->config->connectObjectRepositories[$type]);
        return $repository->findByTypedId($typedId);
    }

    /**
     * @param string $type
     * @param string $query
     * @return ConnectableObject[]
     */
    public function getConnectableObjectByQuery(string $type, string $query): array
    {
        /** @var ConnectableObjectRepository $repository */
        $repository = $this->application->get($this->config->connectObjectRepositories[$type]);
        return $repository->findByQuery($query);
    }

    public function getConnectAccessibility(): ConnectAccessibility
    {
        return $this->application->get(ConnectAccessibility::class);
    }

    public static function getIdentity(ConnectableObject $object): string
    {
        return $object->getConnectorType() . '_' . $object->getConnectorTypedId();
    }

    public static function getTypeFromIdentity(string $identity): string
    {
        $exploded = explode('_', $identity);
        return $exploded[0] ?? '';
    }

    public static function getTypedIdFromIdentity(string $identity): string
    {
        $exploded = explode('_', $identity);
        return $exploded[1] ?? '';
    }
}
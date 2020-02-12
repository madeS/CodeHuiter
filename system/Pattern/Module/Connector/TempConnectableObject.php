<?php

namespace CodeHuiter\Pattern\Module\Connector;

use CodeHuiter\Config\ConnectorConfig;

class TempConnectableObject implements ConnectableObject
{
    public function getConnectorType(): string
    {
        return ConnectorConfig::TYPE_TEMP;
    }

    public function getConnectorTypedId(): string
    {
        return 0;
    }

    public function getConnectorName(): string
    {
        return 'Server';
    }
}

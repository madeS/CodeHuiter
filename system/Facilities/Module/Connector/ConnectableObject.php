<?php

namespace CodeHuiter\Facilities\Module\Connector;

/**
 * To that objects can be connected some other objects like comments
 */
interface ConnectableObject
{
    public function getConnectorType(): string;

    public function getConnectorTypedId(): string;

    public function getConnectorName(): string;
}
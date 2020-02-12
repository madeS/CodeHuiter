<?php

namespace CodeHuiter\Pattern\Module\Connector;

interface ConnectableObject
{
    public function getConnectorType(): string;

    public function getConnectorTypedId(): string;

    public function getConnectorName(): string;
}
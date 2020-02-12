<?php

namespace CodeHuiter\Facilities\Module\Connector;

interface ConnectableObject
{
    public function getConnectorType(): string;

    public function getConnectorTypedId(): string;

    public function getConnectorName(): string;
}
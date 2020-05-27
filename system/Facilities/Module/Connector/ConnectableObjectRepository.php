<?php

namespace CodeHuiter\Facilities\Module\Connector;

/**
 * That repositories provide objects that can be connected by type
 */
interface ConnectableObjectRepository
{
    public function findByTypedId(string $typedId): ?ConnectableObject;

    /**
     * @param string $query
     * @return ConnectableObject[]
     */
    public function findByQuery(string $query): array;
}
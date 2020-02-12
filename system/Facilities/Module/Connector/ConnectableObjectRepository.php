<?php

namespace CodeHuiter\Facilities\Module\Connector;

interface ConnectableObjectRepository
{
    public function findByTypedId(string $typedId): ?ConnectableObject;

    /**
     * @param string $query
     * @return ConnectableObject[]
     */
    public function findByQuery(string $query): array;
}
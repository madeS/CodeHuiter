<?php

namespace CodeHuiter\Pattern\Module\Connector;

interface ConnectableObjectRepository
{
    public function findByTypedId(string $typedId): ?ConnectableObject;

    /**
     * @param string $query
     * @return ConnectableObject[]
     */
    public function findByQuery(string $query): array;
}
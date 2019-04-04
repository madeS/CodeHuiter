<?php

namespace CodeHuiter\Pattern\Modules\Auth\Models;

interface UserRepositoryInterface
{
    /**
     * @return UserInterface
     */
    public function newInstance(): UserInterface;

    /**
     * @param array $where
     * @param array $opt
     * @return UserInterface[]
     */
    public function find(array $where, array $opt = []): array;

    /**
     * @param array $where
     * @param array $opt
     * @return UserInterface|null
     */
    public function findOne(array $where, array $opt = []): ?UserInterface;
}

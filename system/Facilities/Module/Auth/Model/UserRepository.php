<?php

namespace CodeHuiter\Facilities\Module\Auth\Model;

use CodeHuiter\Facilities\Module\Connector\ConnectableObjectRepository;

interface UserRepository extends ConnectableObjectRepository
{
    /**
     * @return User
     */
    public function newInstance(): User;

    public function getById(string $id): ?User;

    /**
     * @param array $where
     * @param array $opt
     * @return User[]
     */
    public function find(array $where, array $opt = []): array;

    /**
     * @param array $where
     * @param array $opt
     * @return User|null
     */
    public function findOne(array $where, array $opt = []): ?User;

    /**
     * @param array $where
     * @param array $set
     */
    public function update(array $where, array $set): void;

    /**
     * @param User $user
     * @return User
     */
    public function save(User $user): User;

    /**
     * @param User $user
     */
    public function delete(User $user): void;
}

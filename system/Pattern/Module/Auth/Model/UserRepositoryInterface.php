<?php

namespace CodeHuiter\Pattern\Module\Auth\Model;

interface UserRepositoryInterface
{
    /**
     * @return UserInterface
     */
    public function newInstance(): UserInterface;

    /**
     * @param int $id
     * @return UserInterface|null
     */
    public function getById(int $id): ?UserInterface;

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

    /**
     * @param UserInterface $user
     * @return UserInterface
     */
    public function save(UserInterface $user): UserInterface;

    /**
     * @param UserInterface $user
     */
    public function delete(UserInterface $user): void;
}

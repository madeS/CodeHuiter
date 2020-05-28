<?php

namespace CodeHuiter\Facilities\Module\Auth\Model;

use CodeHuiter\Core\Application;
use CodeHuiter\Database\RelationalRepository;
use CodeHuiter\Facilities\Module\Connector\ConnectableObject;
use CodeHuiter\Facilities\Module\Connector\ConnectableObjectRepository;
use CodeHuiter\Facilities\Service\RelationalRepositoryProvider;

class UserRepository implements ConnectableObjectRepository
{
    /**
     * @var RelationalRepository
     */
    private $repository;

    /**
     * @param Application $application
     */
    public function __construct(Application $application)
    {
        /** @var RelationalRepositoryProvider $repositoryProvider */
        $repositoryProvider = $application->get(RelationalRepositoryProvider::class);
        $this->repository = $repositoryProvider->get(User::class);
    }

    public function getRelationalRepository(): RelationalRepository
    {
        return $this->repository;
    }

    public function newInstance(): User
    {
        /** @var User $userModel */
        $userModel = User::emptyModel();
        return $userModel;
    }

    public function getById(string $id): ?User
    {
        /** @var User|null $model */
        $model = $this->repository->getById([$id]);
        return $model;
    }

    public function find(array $where, array $opt = []): array
    {
        /** @var User[] $models */
        $models = $this->repository->find($where, $opt);
        return $models;
    }

    public function findOne(array $where, array $opt = []): ?User
    {
        /** @var User|null $model */
        $model = $this->repository->findOne($where, $opt);
        return $model;
    }

    public function update(array $where, array $set): void
    {
        $this->repository->update($where, $set);
    }

    public function save(User $user): User
    {
        if (!$user->getId()) {
            $user->setRegtime($this->repository->getDateService()->sqlTime());
        }
        /** @var User|null $model */
        $model = $this->repository->save($user);
        return $model;
    }

    public function delete(User $user): void
    {
        $this->repository->delete($user);
    }

    public function findByTypedId(string $typedId): ?ConnectableObject
    {
        return $this->getById($typedId);
    }

    public function findByQuery(string $query): array
    {
        $keys = [
            User::FIELD_ID,
            User::FIELD_NAME,
            User::FIELD_LOGIN,
            User::FIELD_EMAIL,
            User::FIELD_FIRST_NAME,
            User::FIELD_LAST_NAME,
        ];

        return $this->find([implode(',', $keys) => $query]);
    }
}

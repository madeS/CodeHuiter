<?php

namespace CodeHuiter\Facilities\Module\Auth\Model;

use CodeHuiter\Core\Application;
use CodeHuiter\Database\RelationalRepository;
use CodeHuiter\Facilities\Module\Connector\ConnectableObject;
use CodeHuiter\Facilities\Module\Connector\ConnectableObjectRepository;
use CodeHuiter\Service\RelationalRepositoryProvider;

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

    /**
     * @param array $where
     * @return User[]
     */
    public function find(array $where): array
    {
        return $this->repository->find($where, []);
    }

    public function findOne(array $where): ?User
    {
        /** @var User|null $model */
        $model = $this->repository->findOne($where, []);
        return $model;
    }

    public function update(array $where, array $set): void
    {
        $this->repository->update($where, $set);
    }

    public function save(User $model): void
    {
        if (!$model->getId()) {
            $model->setRegtime($this->repository->getDateService()->sqlTime());
        }
        $this->repository->save($model);
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

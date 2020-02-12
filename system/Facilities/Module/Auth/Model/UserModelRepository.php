<?php

namespace CodeHuiter\Facilities\Module\Auth\Model;

use CodeHuiter\Core\Application;
use CodeHuiter\Database\RelationalModelRepository;
use CodeHuiter\Exception\Runtime\RuntimeWrongClassException;
use CodeHuiter\Facilities\Module\Auth\Event\UserDeletingEvent;
use CodeHuiter\Facilities\Module\Connector\ConnectableObject;
use CodeHuiter\Service\EventDispatcher;

class UserModelRepository implements UserRepository
{
    /**
     * @var Application
     */
    private $application;

    /**
     * @var RelationalModelRepository
     */
    private $repository;

    /**
     * @param Application $application
     */
    public function __construct(Application $application)
    {
        $this->application = $application;
        $this->repository = new RelationalModelRepository($application, new UserModel());
    }

    public function newInstance(): User
    {
        /** @var User $userModel */
        $userModel = UserModel::getEmpty();
        return $userModel;
    }

    public function getById(string $id): ?User
    {
        /** @var UserModel|null $model */
        $model = $this->repository->getById([$id]);
        return $model;
    }

    public function find(array $where, array $opt = []): array
    {
        /** @var UserModel[] $models */
        $models = $this->repository->find($where, $opt);
        return $models;
    }

    public function findOne(array $where, array $opt = []): ?User
    {
        /** @var UserModel|null $model */
        $model = $this->repository->findOne($where, $opt);
        return $model;
    }

    public function update(array $where, array $set): void
    {
        $this->repository->update($where, $set);
    }

    public function save(User $user): User
    {
        if ($user instanceof UserModel) {
            if (!$user->getId()) {
                $user->setRegtime($this->repository->getDateService()->sqlTime());
            }
            /** @var UserModel|null $model */
            $model = $this->repository->save($user);
            return $model;
        }
        throw RuntimeWrongClassException::wrongObjectGot(UserModel::class, $user);
    }

    public function delete(User $user): void
    {
        if ($user instanceof UserModel) {
            $this->getEventDispatcher()->fire(new UserDeletingEvent($user));
            $this->repository->delete($user);
            return;
        }
        throw RuntimeWrongClassException::wrongObjectGot(UserModel::class, $user);
    }

    public function findByTypedId(string $typedId): ?ConnectableObject
    {
        return $this->getById($typedId);
    }

    public function findByQuery(string $query): array
    {
        $keys = [
            UserModel::FIELD_ID,
            UserModel::FIELD_NAME,
            UserModel::FIELD_LOGIN,
            UserModel::FIELD_EMAIL,
            UserModel::FIELD_FIRST_NAME,
            UserModel::FIELD_LAST_NAME,
        ];

        return $this->find([implode(',', $keys) => $query]);
    }

    private function getEventDispatcher(): EventDispatcher
    {
        return $this->application->get(EventDispatcher::class);
    }
}

<?php

namespace CodeHuiter\Pattern\Module\Auth\Model;

use CodeHuiter\Core\Application;
use CodeHuiter\Database\RelationalModelRepository;
use CodeHuiter\Exception\Runtime\RuntimeWrongClassException;
use CodeHuiter\Pattern\Module\Auth\Event\UserDeletingEvent;
use CodeHuiter\Service\EventDispatcher;

class UserModelRepository implements UserRepositoryInterface
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

    /**
     * @return UserInterface
     */
    public function newInstance(): UserInterface
    {
        /** @var UserInterface $userModel */
        $userModel = UserModel::getEmpty();
        return $userModel;
    }

    /**
     * {@inheritdoc}
     */
    public function getById(int $id): ?UserInterface
    {
        /** @var UserModel|null $model */
        $model = $this->repository->getById([$id]);
        return $model;
    }

    /**
     * {@inheritdoc}
     */
    public function find(array $where, array $opt = []): array
    {
        /** @var UserModel[] $models */
        $models = $this->repository->find($where, $opt);
        return $models;
    }

    /**
     * {@inheritdoc}
     */
    public function findOne(array $where, array $opt = []): ?UserInterface
    {
        /** @var UserModel|null $model */
        $model = $this->repository->findOne($where, $opt);
        return $model;
    }

    /**
     * {@inheritdoc}
     */
    public function save(UserInterface $user): UserInterface
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

    public function delete(UserInterface $user): void
    {
        if ($user instanceof UserModel) {
            $this->getEventDispatcher()->fire(new UserDeletingEvent($user));
            $this->repository->delete($user);
            return;
        }
        throw RuntimeWrongClassException::wrongObjectGot(UserModel::class, $user);
    }

    private function getEventDispatcher(): EventDispatcher
    {
        return $this->application->get(EventDispatcher::class);
    }
}

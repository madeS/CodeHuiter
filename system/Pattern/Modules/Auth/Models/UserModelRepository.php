<?php

namespace CodeHuiter\Pattern\Modules\Auth\Models;

class UserModelRepository extends AbstractRepository implements UserRepositoryInterface
{
    /**
     * @return UserInterface
     */
    public function newInstance(): UserInterface
    {
        return new UserModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getById(int $id): ?UserInterface
    {
        /** @var UserModel|null $model */
        $model = UserModel::getOneWhere(['id' => $id]);
        return $model;
    }

    /**
     * {@inheritdoc}
     */
    public function find(array $where, array $opt = []): array
    {
        return UserModel::getWhere($where, $opt);
    }

    /**
     * {@inheritdoc}
     */
    public function findOne(array $where, array $opt = []): ?UserInterface
    {
        /** @var UserModel|null $model */
        $model = UserModel::getOneWhere($where, $opt);
        return $model;
    }
}

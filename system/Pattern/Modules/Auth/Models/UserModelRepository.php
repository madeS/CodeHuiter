<?php

namespace CodeHuiter\Pattern\Modules\Auth\Models;

class UserModelRepository extends AbstractRepository implements UserRepositoryInterface
{
    /**
     * @return UserInterface
     */
    public function newInstance(): UserInterface
    {
        $item =  new UserModel();
        return $item;
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

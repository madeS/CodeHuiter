<?php

namespace CodeHuiter\Facilities\Module\Media\Model;

use CodeHuiter\Core\Application;
use CodeHuiter\Database\RelationalRepository;
use CodeHuiter\Config\Database\RelationalRepositoryConfig;
use CodeHuiter\Exception\Runtime\RuntimeWrongClassException;
use CodeHuiter\Facilities\Module\Connector\ConnectableObject;
use CodeHuiter\Facilities\Module\Connector\ConnectableObjectRepository;
use CodeHuiter\Service\RelationalRepositoryProvider;

class MediaRepository implements ConnectableObjectRepository
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
        $this->repository = $repositoryProvider->get(Media::class);
    }

    public function getConfig(): RelationalRepositoryConfig
    {
        return $this->repository->getConfig();
    }

    /**
     * @return Media
     */
    public function newInstance(): Media
    {
        /** @var Media $mediaModel */
        $mediaModel = Media::emptyModel();
        return $mediaModel;
    }

    public function getById(string $id): ?Media
    {
        /** @var Media|null $model */
        $model = $this->repository->getById([$id]);
        return $model;
    }

    /**
     * {@inheritdoc}
     */
    public function find(array $where, array $opt = []): array
    {
        /** @var Media[] $models */
        $models = $this->repository->find($where, $opt);
        return $models;
    }

    public function findOne(array $where, array $opt = []): ?Media
    {
        /** @var Media|null $model */
        $model = $this->repository->findOne($where, $opt);
        return $model;
    }

    public function update(array $where, array $set): void
    {
        $this->repository->update($where, $set);
    }

    public function save(Media $media): Media
    {
        $this->repository->save($media);
        return $media;
    }

    public function delete(Media $media): void
    {
        if ($media instanceof Media) {
            $this->repository->delete($media);
            return;
        }
        throw RuntimeWrongClassException::wrongObjectGot(Media::class, $media);
    }

    public function findByTypedId(string $typedId): ?ConnectableObject
    {
        return $this->getById($typedId);
    }

    public function findByQuery(string $query): array
    {
        $keys = [
            Media::FIELD_ID,
            Media::FIELD_TITLE,
            Media::FIELD_DESCRIPTION,
        ];

        return $this->find([implode(',', $keys) => $query]);
    }
}

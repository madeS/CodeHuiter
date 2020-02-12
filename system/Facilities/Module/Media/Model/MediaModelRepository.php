<?php

namespace CodeHuiter\Facilities\Module\Media\Model;

use CodeHuiter\Core\Application;
use CodeHuiter\Database\RelationalModelRepository;
use CodeHuiter\Exception\Runtime\RuntimeWrongClassException;
use CodeHuiter\Facilities\Module\Connector\ConnectableObject;
use CodeHuiter\Service\ByDefault\EventDispatcher\RelationalModelUpdatedEvent;
use CodeHuiter\Service\EventDispatcher;

class MediaModelRepository implements MediaRepository
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
        $this->repository = new RelationalModelRepository($application, new MediaModel());
    }

    /**
     * @return Media
     */
    public function newInstance(): Media
    {
        /** @var Media $mediaModel */
        $mediaModel = MediaModel::getEmpty();
        return $mediaModel;
    }

    public function getById(string $id): ?Media
    {
        /** @var MediaModel|null $model */
        $model = $this->repository->getById([$id]);
        return $model;
    }

    /**
     * {@inheritdoc}
     */
    public function find(array $where, array $opt = []): array
    {
        /** @var MediaModel[] $models */
        $models = $this->repository->find($where, $opt);
        return $models;
    }

    /**
     * {@inheritdoc}
     */
    public function findOne(array $where, array $opt = []): ?Media
    {
        /** @var MediaModel|null $model */
        $model = $this->repository->findOne($where, $opt);
        return $model;
    }

    public function update(array $where, array $set): void
    {
        $this->repository->update($where, $set);
    }

    /**
     * {@inheritdoc}
     */
    public function save(Media $media): Media
    {
        if ($media instanceof MediaModel) {
            /** @var MediaModel|null $model */
            $model = $this->repository->save($media);

            return $model;
        }
        throw RuntimeWrongClassException::wrongObjectGot(MediaModel::class, $media);
    }

    public function delete(Media $media): void
    {
        if ($media instanceof MediaModel) {
            //$this->getEventDispatcher()->fire(new MediaDeletingEvent($media));
            $this->repository->delete($media);
            return;
        }
        throw RuntimeWrongClassException::wrongObjectGot(MediaModel::class, $media);
    }

    public function findByTypedId(string $typedId): ?ConnectableObject
    {
        return $this->getById($typedId);
    }

    public function findByQuery(string $query): array
    {
        $keys = [
            MediaModel::FIELD_ID,
            MediaModel::FIELD_TITLE,
            MediaModel::FIELD_DESCRIPTION,
        ];

        return $this->find([implode(',', $keys) => $query]);
    }

    private function getEventDispatcher(): EventDispatcher
    {
        return $this->application->get(EventDispatcher::class);
    }
}

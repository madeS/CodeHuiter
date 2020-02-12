<?php

namespace CodeHuiter\Facilities\Module\Media\Model;

use CodeHuiter\Facilities\Module\Connector\ConnectableObjectRepository;

interface MediaRepository extends ConnectableObjectRepository
{
    /**
     * @return Media
     */
    public function newInstance(): Media;

    public function getById(string $id): ?Media;

    /**
     * @param array $where
     * @param array $opt
     * @return Media[]
     */
    public function find(array $where, array $opt = []): array;

    /**
     * @param array $where
     * @param array $opt
     * @return Media|null
     */
    public function findOne(array $where, array $opt = []): ?Media;

    /**
     * @param array $where
     * @param array $set
     */
    public function update(array $where, array $set): void;

    /**
     * @param Media $media
     * @return Media
     */
    public function save(Media $media): Media;

    /**
     * @param Media $media
     */
    public function delete(Media $media): void;
}

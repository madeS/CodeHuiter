<?php

namespace CodeHuiter\Facilities\Module\Media\Model;

interface Album
{
    public function getId(): int;

    public function getActive(): int;

    public function setActive(int $active): void;

    public function getType(): string;

    public function setType(string $type): void;

    public function getUserId(): int;

    public function setUserId(int $user_id): void;

    public function getTitle(): string;

    public function setTitle(string $title): void;

    public function getAlias(): string;

    public function setAlias(string $alias): void;

    public function getCategory(): string;

    public function setCategory(string $category): void;

    public function getObjectType(): string;

    public function setObjectType(string $object_type): void;

    public function getObjectId(): int;

    public function setObjectId(int $object_id): void;

    public function getPictureId(): int;

    public function setPictureId(int $picture_id): void;

    public function getPicturePreview(): string;

    public function setPicturePreview(string $picture_preview): void;

    public function getDescription(): string;

    public function setDescription(string $description): void;

    public function getContent(): string;

    public function setContent(string $content): void;

    public function getMediasCount(): int;

    public function setMediasCount(int $medias_count): void;

    public function getCreatedAt(): string;

    public function setCreatedAt(string $created_at): void;

    public function getUpdatedAt(): string;

    public function setUpdatedAt(string $updated_at): void;

    public function getShowAt(): string;

    public function setShowAt(string $show_at): void;
}

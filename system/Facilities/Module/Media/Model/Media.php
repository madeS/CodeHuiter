<?php

namespace CodeHuiter\Facilities\Module\Media\Model;

use CodeHuiter\Facilities\Module\Connector\ConnectableObject;

interface Media extends ConnectableObject
{
    public const TYPE_PHOTO = 'photo';
    public const TYPE_VIDEO = 'video';
    public const TYPE_ZIP = 'zip';

    public function getId(): string;

    public function getActive(): int;

    public function setActive(bool $active): void;

    public function getType(): string;

    public function setType(string $type): void;

    public function getUserId(): string;

    public function setUserId(string $user_id): void;

    public function getObjectType(): string;

    public function setObjectType(string $object_type): void;

    public function getObjectId(): int;

    public function setObjectId(int $object_id): void;

    public function getCreatedAt(): string;

    public function setCreatedAt(string $created_at): void;

    public function getUpdatedAt(): string;

    public function setUpdatedAt(string $updated_at): void;

    public function getSortnum(): int;

    public function setSortnum(int $sortnum): void;

    public function getTitle(): string;

    public function setTitle(string $title): void;

    public function getDescription(): string;

    public function setDescription(string $description): void;

    public function getPictureOrig(): string;

    public function setPictureOrig(string $picture_orig): void;

    public function getPicture(): string;

    public function setPicture(string $picture): void;

    public function getPicturePreview(): string;

    public function setPicturePreview(string $picture_preview): void;

    public function getPreviewParams(): string;

    public function setPreviewParams(string $preview_params): void;

    public function getPictureData(): string;

    public function setPictureData(string $picture_data): void;

    public function getVideoSource(): string;

    public function setVideoSource(string $video_source): void;

    public function getVideoCode(): string;

    public function setVideoCode(string $video_code): void;

    public function getVideoEmbed(): string;

    public function setVideoEmbed(string $video_embed): void;

    public function getVideoDuration(): int;

    public function setVideoDuration(int $video_duration): void;

    public function getContentSize(): int;

    public function setContentSize(int $content_size): void;
}

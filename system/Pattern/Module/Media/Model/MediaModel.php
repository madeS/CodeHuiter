<?php

namespace CodeHuiter\Pattern\Module\Media\Model;

use CodeHuiter\Config\ConnectorConfig;
use CodeHuiter\Database\RelationalModel;

class MediaModel extends RelationalModel implements Media
{
    public const FIELD_ID = 'id';
    public const FIELD_TITLE = 'title';
    public const FIELD_TYPE = 'type';
    public const FIELD_DESCRIPTION = 'description';
    public const FIELD_USER_ID = 'user_id';
    public const FIELD_SORT_NUMBER = 'sortnum';

    private const DEFAULT_PICTURE = 'default/empty.png';
    protected $_table = 'user_medias';
    protected $_databaseServiceKey = 'db';
    protected $_primaryFields = ['id'];
    protected $_autoIncrementField = 'id';

    /** @var int */
    protected $id;
    /** @var int */
    protected $active = 1;
    /** @var string */
    protected $type = 'unknown';
    /** @var int */
    protected $user_id = 0;
    /** @var string */
    protected $object_type = 'unknown';
    /** @var int */
    protected $object_id = 0;
    /** @var string */
    protected $created_at;
    /** @var string */
    protected $updated_at;
    /** @var int */
    protected $sortnum = 0;
    /** @var string */
    protected $title = '';
    /** @var string */
    protected $description = '';
    /** @var string */
    protected $picture_orig = self::DEFAULT_PICTURE;
    /** @var string */
    protected $picture = self::DEFAULT_PICTURE;
    /** @var string */
    protected $picture_preview = self::DEFAULT_PICTURE;
    /** @var string */
    protected $preview_params = '[]';
    /** @var string */
    protected $picture_data = '';
    /** @var string */
    protected $video_source = '';
    /** @var string */
    protected $video_code = '';
    /** @var string */
    protected $video_embed = '';
    /** @var int */
    protected $video_duration = 0;
    /** @var int */
    protected $content_size = 0;

    public function getId(): string
    {
        return $this->id;
    }

    public function getActive(): int
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = (int)$active;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getUserId(): string
    {
        return $this->user_id;
    }

    public function setUserId(string $user_id): void
    {
        $this->user_id = $user_id;
    }

    public function getObjectType(): string
    {
        return $this->object_type;
    }

    public function setObjectType(string $object_type): void
    {
        $this->object_type = $object_type;
    }

    public function getObjectId(): int
    {
        return $this->object_id;
    }

    public function setObjectId(int $object_id): void
    {
        $this->object_id = $object_id;
    }

    public function getCreatedAt(): string
    {
        return $this->created_at;
    }

    public function setCreatedAt(string $created_at): void
    {
        $this->created_at = $created_at;
    }

    public function getUpdatedAt(): string
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(string $updated_at): void
    {
        $this->updated_at = $updated_at;
    }

    public function getSortnum(): int
    {
        return $this->sortnum;
    }

    public function setSortnum(int $sortnum): void
    {
        $this->sortnum = $sortnum;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getPictureOrig(): string
    {
        return $this->picture_orig;
    }

    public function setPictureOrig(string $picture_orig): void
    {
        $this->picture_orig = $picture_orig;
    }

    public function getPicture(): string
    {
        return $this->picture;
    }

    public function setPicture(string $picture): void
    {
        $this->picture = $picture;
    }

    public function getPicturePreview(): string
    {
        return $this->picture_preview;
    }

    public function setPicturePreview(string $picture_preview): void
    {
        $this->picture_preview = $picture_preview;
    }

    public function getPreviewParams(): string
    {
        return $this->preview_params;
    }

    public function setPreviewParams(string $preview_params): void
    {
        $this->preview_params = $preview_params;
    }

    public function getPictureData(): string
    {
        return $this->picture_data;
    }

    public function setPictureData(string $picture_data): void
    {
        $this->picture_data = $picture_data;
    }

    public function getVideoSource(): string
    {
        return $this->video_source;
    }

    public function setVideoSource(string $video_source): void
    {
        $this->video_source = $video_source;
    }

    public function getVideoCode(): string
    {
        return $this->video_code;
    }

    public function setVideoCode(string $video_code): void
    {
        $this->video_code = $video_code;
    }

    public function getVideoEmbed(): string
    {
        return $this->video_embed;
    }

    public function setVideoEmbed(string $video_embed): void
    {
        $this->video_embed = $video_embed;
    }

    public function getVideoDuration(): int
    {
        return $this->video_duration;
    }

    public function setVideoDuration(int $video_duration): void
    {
        $this->video_duration = $video_duration;
    }

    public function getContentSize(): int
    {
        return $this->content_size;
    }

    public function setContentSize(int $content_size): void
    {
        $this->content_size = $content_size;
    }

    public function getConnectorType(): string
    {
        return ConnectorConfig::TYPE_PHOTO;
    }

    public function getConnectorTypedId(): string
    {
        return $this->getId();
    }

    public function getConnectorName(): string
    {
        return "[{$this->getId()}] {$this->getTitle()}";
    }
}

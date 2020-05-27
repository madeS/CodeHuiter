<?php

namespace CodeHuiter\Facilities\Module\Media\Model;

use CodeHuiter\Database\Model;

class Album extends Model
{
    private const DEFAULT_PICTURE = 'default/empty.png';

    /** @var int */
    protected $id;
    /** @var int */
    protected $active = 1;
    /** @var string */
    protected $type = 'unknown';
    /** @var int */
    protected $user_id = 0;

    /** @var string */
    protected $title= '';
    /** @var string */
    protected $alias = '';
    /** @var string */
    protected $category = 'default';

    /** @var string */
    protected $object_type = 'unknown';
    /** @var int */
    protected $object_id = 0;

    /** @var int */
    protected $picture_id = 0;
    /** @var string */
    protected $picture_preview = self::DEFAULT_PICTURE;

    /** @var string */
    protected $description = '';
    /** @var string */
    protected $content = '';
    /** @var int */
    protected $medias_count = '';

    /** @var string */
    protected $created_at;
    /** @var string */
    protected $updated_at;
    /** @var string */
    protected $show_at;

    public function getId(): int
    {
        return $this->id;
    }

    public function getActive(): int
    {
        return $this->active;
    }

    public function setActive(int $active): void
    {
        $this->active = $active;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function setUserId(int $user_id): void
    {
        $this->user_id = $user_id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getAlias(): string
    {
        return $this->alias;
    }

    public function setAlias(string $alias): void
    {
        $this->alias = $alias;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function setCategory(string $category): void
    {
        $this->category = $category;
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

    public function getPictureId(): int
    {
        return $this->picture_id;
    }

    public function setPictureId(int $picture_id): void
    {
        $this->picture_id = $picture_id;
    }

    public function getPicturePreview(): string
    {
        return $this->picture_preview;
    }

    public function setPicturePreview(string $picture_preview): void
    {
        $this->picture_preview = $picture_preview;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getMediasCount(): int
    {
        return $this->medias_count;
    }

    public function setMediasCount(int $medias_count): void
    {
        $this->medias_count = $medias_count;
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

    public function getShowAt(): string
    {
        return $this->show_at;
    }

    public function setShowAt(string $show_at): void
    {
        $this->show_at = $show_at;
    }
}

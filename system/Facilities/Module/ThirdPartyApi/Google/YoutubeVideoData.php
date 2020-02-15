<?php

namespace CodeHuiter\Facilities\Module\ThirdPartyApi\Google;

class YoutubeVideoData
{
    /** @var string */
    private $id;
    /** @var string */
    private $authorName;
    /** @var string */
    private $authorId;
    /** @var string */
    private $title;
    /** @var string */
    private $description;
    /** @var int */
    private $duration;
    /** @var string */
    private $picture;
    /** @var string[] */
    private $thumbnails;
    /** @var int */
    private $viewCount;
    /** @var int */
    private $likeCount;
    /** @var int */
    private $dislikeCount;
    /** @var int */
    private $commentCount;

    public function __construct(
        string $id,
        string $authorName,
        string $authorId,
        string $title,
        string $description,
        int $duration,
        string $picture,
        array $thumbnails,
        int $viewCount,
        int $likeCount,
        int $dislikeCount,
        int $commentCount
    ) {
        $this->id = $id;
        $this->authorName = $authorName;
        $this->authorId = $authorId;
        $this->title = $title;
        $this->description = $description;
        $this->duration = $duration;
        $this->picture = $picture;
        $this->thumbnails = $thumbnails;
        $this->viewCount = $viewCount;
        $this->likeCount = $likeCount;
        $this->dislikeCount = $dislikeCount;
        $this->commentCount = $commentCount;
    }


    public function getId(): string
    {
        return $this->id;
    }

    public function getType(): string
    {
        return 'youtube';
    }

    public function getAuthorName(): string
    {
        return $this->authorName;
    }

    public function getAuthorId(): string
    {
        return $this->authorId;
    }

    public function getAuthorUrl(): string
    {
        return 'https://www.youtube.com/channel/' . $this->authorId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getEmbedUrl(): string
    {
        return 'http://www.youtube.com/embed/' . $this->id;
    }

    public function getEmbedData(): string
    {
        return $this->id;
    }

    public function getDuration(): int
    {
        return $this->duration;
    }

    public function getPicture(): string
    {
        return $this->picture;
    }

    /**
     * @return string[]
     */
    public function getThumbnails(): array
    {
        return $this->thumbnails;
    }

    public function getViewCount(): int
    {
        return $this->viewCount;
    }

    public function getLikeCount(): int
    {
        return $this->likeCount;
    }

    public function getDislikeCount(): int
    {
        return $this->dislikeCount;
    }

    public function getCommentCount(): int
    {
        return $this->commentCount;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'authorName' => $this->authorName,
            'authorId' => $this->authorId,
            'title' => $this->title,
            'description' => $this->description,
            'duration' => $this->duration,
            'picture' => $this->picture,
            'thumbnails' => $this->thumbnails,
            'viewCount' => $this->viewCount,
            'likeCount' => $this->likeCount,
            'dislikeCount' => $this->dislikeCount,
            'commentCount' => $this->commentCount,
        ];
    }
}

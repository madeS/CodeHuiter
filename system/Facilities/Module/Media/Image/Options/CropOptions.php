<?php

namespace CodeHuiter\Facilities\Module\Media\Image\Options;

class CropOptions
{
    /**
     * @var int|null
     */
    private $sourceOffsetX;
    /**
     * @var int|null
     */
    private $sourceOffsetY;
    /**
     * @var int|null
     */
    private $sourceWidthCrop;
    /**
     * @var int|null
     */
    private $sourceHeightCrop;

    /**
     * Parameters for image crop
     * @param int|null $sourceOffsetX Offset by X of original image for CROP
     * @param int|null $sourceOffsetY Offset by Y of original image for CROP
     * @param int|null $sourceWidthCrop Width of CROP on original image
     * @param int|null $sourceHeightCrop Height of CROP on original image
     */
    public function __construct(
        int $sourceOffsetX,
        int $sourceOffsetY,
        int $sourceWidthCrop,
        int $sourceHeightCrop
    ) {
        $this->sourceOffsetX = $sourceOffsetX;
        $this->sourceOffsetY = $sourceOffsetY;
        $this->sourceWidthCrop = $sourceWidthCrop;
        $this->sourceHeightCrop = $sourceHeightCrop;
    }

    public static function fromArray(array $cropResult): self
    {

    }

    public function getSourceOffsetX(): int
    {
        return $this->sourceOffsetX;
    }

    public function getSourceOffsetY(): int
    {
        return $this->sourceOffsetY;
    }

    public function getSourceWidthCrop(): int
    {
        return $this->sourceWidthCrop;
    }

    public function getSourceHeightCrop(): int
    {
        return $this->sourceHeightCrop;
    }
}

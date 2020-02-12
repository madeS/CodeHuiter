<?php

namespace CodeHuiter\Pattern\Module\Media\Image\Options;

class ResizeOptions
{
    /**
     * Resize without proportions
     */
    public const RESIZE_TYPE_SIMPLE = 0;
    /**
     * Resize by width
     */
    public const RESIZE_TYPE_WIDTH = 1;
    /**
     * Resize by height
     */
    public const RESIZE_TYPE_HEIGHT = 2;
    /**
     * Resize by maximum container. Result can be less than container by one side
     */
    public const RESIZE_TYPE_MAXIMUM = 3;
    /**
     * Resize by maximum container. Result can be less than container by both side
     */
    public const RESIZE_TYPE_MAXIMUM_WITH_SAVE_SMALL = 4;
    /**
     * Resize by window container. Result image can be cut by width or height
     */
    public const RESIZE_TYPE_CROP = 5;

    /**
     * @var int
     */
    private $targetWidth;
    /**
     * @var int
     */
    private $targetHeight;
    /**
     * @var int
     */
    private $resizeType;
    /**
     * @var int
     */
    private $qualityJpegPercent;
    /**
     * @var bool
     */
    private $allowCopyOriginal;
    /**
     * @var CropOptions|null
     */
    private $cropParameters;
    /**
     * @var WatermarkOptions|null
     */
    private $watermarkOptions;

    public function __construct(
        int $targetWidth,
        int $targetHeight,
        int $resizeType = self::RESIZE_TYPE_SIMPLE,
        int $qualityJpegPercent = 85,
        bool $allowCopyOriginal = false,
        ?CropOptions $cropParameters = null,
        ?WatermarkOptions $watermarkOptions = null
    ) {
        $this->targetWidth = $targetWidth;
        $this->targetHeight = $targetHeight;
        $this->resizeType = $resizeType;
        $this->qualityJpegPercent = $qualityJpegPercent;
        $this->allowCopyOriginal = $allowCopyOriginal;
        $this->cropParameters = $cropParameters;
        $this->watermarkOptions = $watermarkOptions;
    }

    public function getTargetWidth(): int
    {
        return $this->targetWidth;
    }

    public function getTargetHeight(): int
    {
        return $this->targetHeight;
    }

    public function getResizeType(): int
    {
        return $this->resizeType;
    }

    public function getQualityJpegPercent(): int
    {
        return $this->qualityJpegPercent;
    }

    public function isAllowCopyOriginal(): bool
    {
        return $this->allowCopyOriginal;
    }

    public function getCropParameters(): ?CropOptions
    {
        return $this->cropParameters;
    }

    public function getWatermarkOptions(): ?WatermarkOptions
    {
        return $this->watermarkOptions;
    }
}

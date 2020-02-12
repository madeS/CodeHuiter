<?php

namespace CodeHuiter\Facilities\Module\Media\Image\Options;

class WatermarkImageOptions implements WatermarkOptions
{
    public const POSITION_Y_CENTER = 'center';
    public const POSITION_Y_TOP = 'top';
    public const POSITION_Y_BOTTOM = 'bottom';

    public const POSITION_X_CENTER = 'center';
    public const POSITION_X_LEFT = 'left';
    public const POSITION_X_RIGHT = 'right';

    /** @var string */
    private $pngFile;

    /** @var int */
    private $percent;

    /** @var string */
    private $positionX;

    /** @var string */
    private $positionY;

    public function __construct(string $pngFile, int $percent, string $positionX, string $positionY)
    {
        $this->pngFile = $pngFile;
        $this->percent = $percent;
        $this->positionX = $positionX;
        $this->positionY = $positionY;
    }

    public function getPngFile(): string
    {
        return $this->pngFile;
    }

    public function getPercent(): int
    {
        return $this->percent;
    }

    public function getPositionX(): string
    {
        return $this->positionX;
    }

    public function getPositionY(): string
    {
        return $this->positionY;
    }
}

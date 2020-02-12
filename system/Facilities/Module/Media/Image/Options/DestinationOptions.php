<?php

namespace CodeHuiter\Facilities\Module\Media\Image\Options;

use CodeHuiter\Facilities\Module\Media\Image\ImageProperties;
use CodeHuiter\Service\DateService;

class DestinationOptions
{
    public const FORMAT_OLD = 0;
    public const FORMAT_PNG = ImageProperties::FORMAT_PNG;
    public const FORMAT_JPEG = ImageProperties::FORMAT_JPEG;
    public const FORMAT_GIF = ImageProperties::FORMAT_GIF;

    /**
     * @var string
     */
    private $baseDirectory;

    /**
     * @var string
     */
    private $innerDirectory;

    /**
     * @var string
     */
    private $title;

    /**
     * @var int
     */
    private $format;

    /**
     * Copy image to $baseDirectory / $innerDirectory / $title . $format
     * @param string $baseDirectory Base directory for copy to
     * @param string $innerDirectory Additional directory to copy (if not exist will be created)
     * @param string $title The title of image. Next facilities will be replaced:
     * <br/> {#rand} - 32 random symbol from md5
     * <br/> {#rand6} - 6 random symbols from md5
     * <br/> {#timestamp} - timestamp
     * <br/> {#date} - time in yyyymmdd
     * <br/> {#time} - time in hhiiss
     * @param int $format Save format o image
     * <br/> Destination::FORMAT_PNG - PNG
     * <br/> Destination::FORMAT_JPEG - JPEG
     * <br/> Destination::FORMAT_GIF - GIF
     */
    public function __construct(
        string $baseDirectory,
        string $innerDirectory,
        string $title,
        int $format = self::FORMAT_OLD
    ) {
        $this->baseDirectory = rtrim($baseDirectory, '/') . '/';
        $this->innerDirectory = trim($innerDirectory, '/') . '/';
        $this->title = $title;
        $this->format = $format;
    }

    public function getBaseDirectory(): string
    {
        return $this->baseDirectory;
    }

    public function getInnerDirectory(): string
    {
        return $this->innerDirectory;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getFormat(): int
    {
        return $this->format;
    }
}

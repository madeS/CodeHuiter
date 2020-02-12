<?php

namespace CodeHuiter\Pattern\Module\Media\Image;

use CodeHuiter\Core\Response;
use CodeHuiter\Exception\CodeHuiterRuntimeException;
use Exception;

class ImageProperties
{
    public const FORMAT_PNG = 1;
    public const FORMAT_JPEG = 2;
    public const FORMAT_GIF = 3;

    /**
     * @var string
     */
    private $fileName;
    /**
     * @var int
     */
    private $width;
    /**
     * @var int
     */
    private $height;
    /**
     * @var int
     */
    private $format;

    public function __construct(string $fileName, int $width, int $height, int $type = self::FORMAT_PNG)
    {
        $this->fileName = $fileName;
        $this->width = $width;
        $this->height = $height;
        $this->format = $type;
    }

    public static function fromFile(string $file): self
    {
        try {
            $size = getimagesize($file);
            if (!$size) {
                throw new CodeHuiterRuntimeException('Cant get size from file', Response::HTTP_CODE_BAD_REQUEST);
            }
            if (empty($size[0]) || empty($size[1])) {
                throw new CodeHuiterRuntimeException(sprintf('Invalid image size [%s]', print_r($size, true)), Response::HTTP_CODE_BAD_REQUEST);
            }
            $type = null;
            switch ($size['mime']) {
                case 'image/png': $type = self::FORMAT_PNG; break;
                case 'image/jpeg': $type = self::FORMAT_JPEG; break;
                case 'image/gif': $type = self::FORMAT_GIF; break;
                default: throw new CodeHuiterRuntimeException(sprintf('UnRecognize mime [%s]', $size['mime']), Response::HTTP_CODE_BAD_REQUEST);
            }
            return new self($file, $size[0], $size[1], $type);
        } catch (Exception $exception) {
            throw new CodeHuiterRuntimeException('Invalid file', Response::HTTP_CODE_BAD_REQUEST, $exception);
        }
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function getFormat(): int
    {
        return $this->format;
    }
}

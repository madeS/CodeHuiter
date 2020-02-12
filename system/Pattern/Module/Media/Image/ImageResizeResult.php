<?php

namespace CodeHuiter\Pattern\Module\Media\Image;

class ImageResizeResult
{
    /**
     * @var string
     */
    private $baseDirectory;
    /**
     * @var string
     */
    private $relationFile;
    /**
     * @var array
     */
    private $cropResult;

    public function __construct(
        string $baseDirectory,
        string $relationFile,
        array $cropResult
    ) {
        $this->baseDirectory = $baseDirectory;
        $this->relationFile = $relationFile;
        $this->cropResult = $cropResult;
    }

    public function getBaseDirectory(): string
    {
        return $this->baseDirectory;
    }

    public function getRelationFile(): string
    {
        return $this->relationFile;
    }

    /**
     * @return array
     * <br/><b>orig_width</b> - Ширина оригинального изображения
     * <br/><b>orig_height</b> - Высота оригинального изображения
     * <br/><b>src_width</b> - Ширина откропленного части на исходнике
     * <br/><b>src_heigh</b> - Высота откропленного части на исходнике
     * <br/><b>srcx</b> - смещение X на исходной картинке откуда обрезать при CROP
     * <br/><b>srcy</b> - смещение Y на исходной картинке откуда обрезать при CROP
     * <br/><b>new_width</b> - Ширина нового изображения
     * <br/><b>new_height</b> - Высота нового изображения
     */
    public function getCropResult(): array
    {
        return $this->cropResult;
    }
}

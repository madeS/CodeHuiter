<?php

namespace CodeHuiter\Pattern\Module\Media\Image;

use CodeHuiter\Core\Response;
use CodeHuiter\Exception\CodeHuiterRuntimeException;
use CodeHuiter\Modifier\IntModifier;
use CodeHuiter\Modifier\StringModifier;
use CodeHuiter\Pattern\Module\Media\Image\Options\CropOptions;
use CodeHuiter\Pattern\Module\Media\Image\Options\DestinationOptions;
use CodeHuiter\Pattern\Module\Media\Image\Options\ResizeOptions;
use CodeHuiter\Pattern\Module\Media\Image\Options\WatermarkImageOptions;
use CodeHuiter\Pattern\Module\Media\Image\Options\WatermarkTextOptions;
use CodeHuiter\Service\DateService;
use CodeHuiter\Service\FileStorage;
use CodeHuiter\Service\Logger;
use Exception;

class ImageProcessor
{
    private const LOG_TAG = 'IMAGE_PROCESSOR';

    /**
     * @var FileStorage
     */
    private $fileStorage;
    /**
     * @var DateService
     */
    private $dateService;
    /**
     * @var Logger
     */
    private $logger;

    public function __construct(
        FileStorage $fileStorage,
        DateService $dateService,
        Logger $logger
    ) {
        $this->fileStorage = $fileStorage;
        $this->dateService = $dateService;
        $this->logger = $logger;
    }

    public function resize(string $originalFile, DestinationOptions $target, ResizeOptions $resize): ImageResizeResult
    {
        if (!$this->fileStorage->isDirectoryExist($target->getBaseDirectory())) {
            throw new CodeHuiterRuntimeException(sprintf('Directory %s is not exist', $target->getBaseDirectory()));
        }

        $originalImageProperties = $this->getImageProperties($originalFile);

        $targetFormat = $target->getFormat() === DestinationOptions::FORMAT_OLD ? $originalImageProperties->getFormat() : $target->getFormat();
        $targetFileName = $this->getDestinationFileName($originalFile, $target->getTitle(), $targetFormat);
        $relationalTargetFileName = $target->getInnerDirectory() . $targetFileName;

        $targetWidth = $resize->getTargetWidth();
        $targetHeight = $resize->getTargetHeight();
        if ($resize->getResizeType() === ResizeOptions::RESIZE_TYPE_MAXIMUM_WITH_SAVE_SMALL
            && $originalImageProperties->getWidth() < $resize->getTargetWidth()
            && $originalImageProperties->getHeight() < $resize->getTargetHeight()
        ){
            if ($originalImageProperties->getFormat() === ImageProperties::FORMAT_JPEG && $resize->isAllowCopyOriginal()) {
                $this->fileStorage->copyFile($originalFile, $target->getBaseDirectory() . $target->getInnerDirectory(), $targetFileName);
                return new ImageResizeResult($target->getBaseDirectory(), $relationalTargetFileName, []);
            }
            $targetWidth = $originalImageProperties->getWidth();
            $targetHeight = $originalImageProperties->getHeight();
        }
        if (!$targetWidth || !$targetHeight) {
            throw new CodeHuiterRuntimeException(sprintf('Target width = %d or height %d is equal zero', $targetWidth, $targetHeight));
        }

        $cropResult = $this->getFrameCoordinates(
            $resize->getResizeType(),
            $originalImageProperties->getWidth(),
            $originalImageProperties->getHeight(),
            $targetWidth,
            $targetHeight,
            $resize->getCropParameters()
        );

        $sourceFrameWidth = $cropResult['sourceFrameWidth'];
        $sourceFrameHeight = $cropResult['sourceFrameHeight'];
        $sourceFrameOffsetX = $cropResult['sourceFrameOffsetX'];
        $sourceFrameOffsetY = $cropResult['sourceFrameOffsetY'];
        $targetFrameWidth = $cropResult['targetFrameWidth'];
        $targetFrameHeight = $cropResult['targetFrameHeight'];

        $sourceResource = $this->getSourceResource($originalFile, $originalImageProperties->getFormat());
        $targetResource = $this->getTargetResource($targetFrameWidth, $targetFrameHeight);

        imagecopyresampled(
            $targetResource, $sourceResource,
            0,0,
            $sourceFrameOffsetX, $sourceFrameOffsetY,
            $targetFrameWidth, $targetFrameHeight,
            $sourceFrameWidth, $sourceFrameHeight
        );

        $watermark = $resize->getWatermarkOptions();
        if ($watermark instanceof WatermarkTextOptions) {
            if ($watermark->getText()) {
                $color = $watermark->getFontColor();
                $fontFile = $watermark->getFontFile();
                $targetResourceColor = imagecolorallocate($targetResource, $color[0] ?? 255, $color[1] ?? 255, $color[2] ?? 255);
                if ($fontFile) {
                    $textBox = imagettfbbox($watermark->getFontSize(), 0, $fontFile, $watermark->getText());
                    // bl xy, br xy, tr xy, tl xy
                    $tsign_x = $targetFrameWidth - ($textBox[4]-$textBox[0]) - $textBox[6] - 3;
                    $tsign_y = $targetFrameHeight - ($textBox[1]-$textBox[5]) - $textBox[7] - 3;
                    imagettftext($targetResource, $watermark->getFontSize(), 0, $tsign_x, $tsign_y, $targetResourceColor, $fontFile, $watermark->getText());
                }
            }
        }
        if ($watermark instanceof WatermarkImageOptions) {
            $waterMarkPngFile = $watermark->getPngFile();
            if (file_exists($waterMarkPngFile) && is_file($waterMarkPngFile)) {
                $watermarkPercent = $watermark->getPercent();
                $watermarkXPosition = $watermark->getPositionX();
                $watermarkYPosition = $watermark->getPositionY();
                $watermarkImageProperties = $this->getImageProperties($waterMarkPngFile);
                $watermarkResource = $this->getSourceResource($waterMarkPngFile, ImageProperties::FORMAT_PNG);
                if ($watermarkImageProperties->getWidth() && $watermarkImageProperties->getHeight()) {
                    $watermarkImageBoxWidth = (int)($targetFrameWidth * $watermarkPercent / 100);
                    $watermarkImageBoxHeight = (int)($targetFrameHeight * $watermarkPercent / 100);

                    $tsignTargetHeight = $watermarkImageBoxHeight;
                    $tsignTargetWidth = $watermarkImageBoxHeight * $watermarkImageProperties->getWidth() / $watermarkImageProperties->getHeight();
                    if ((1.0 * $watermarkImageBoxWidth / $watermarkImageBoxHeight) < (1.0 * $watermarkImageProperties->getWidth() / $watermarkImageProperties->getHeight()) ) {
                        $tsignTargetWidth = $watermarkImageBoxWidth;
                        $tsignTargetHeight = $watermarkImageBoxWidth * $watermarkImageProperties->getHeight() / $watermarkImageProperties->getWidth();
                    }

                    $tsign_png_dest_x = 0;
                    $tsign_png_dest_y = 0;
                    if ($watermarkXPosition === WatermarkImageOptions::POSITION_X_CENTER) $tsign_png_dest_x = (int)(($targetFrameWidth - $tsignTargetWidth ) / 2);
                    if ($watermarkXPosition === WatermarkImageOptions::POSITION_X_RIGHT) $tsign_png_dest_x = $targetFrameWidth - $tsignTargetWidth;
                    if ($watermarkYPosition === WatermarkImageOptions::POSITION_Y_CENTER) $tsign_png_dest_y = (int)(($targetFrameHeight - $tsignTargetHeight ) / 2);
                    if ($watermarkYPosition === WatermarkImageOptions::POSITION_Y_BOTTOM) $tsign_png_dest_y = $targetFrameHeight - $tsignTargetHeight;
                    imagecopyresampled(
                        $targetResource, $watermarkResource,
                        $tsign_png_dest_x, $tsign_png_dest_y,
                        0, 0,
                        $tsignTargetWidth, $tsignTargetHeight,
                        $watermarkImageProperties->getWidth(), $watermarkImageProperties->getHeight());
                }
            }
        }

        $tempTargetFileName = STORAGE_TEMP_PATH . $targetFileName;
        if ($targetFormat === ImageProperties::FORMAT_JPEG){
            imagejpeg($targetResource, $tempTargetFileName, $resize->getQualityJpegPercent());
        } elseif($targetFormat === ImageProperties::FORMAT_PNG) {
            imagepng($targetResource, $tempTargetFileName);
        } elseif($targetFormat === ImageProperties::FORMAT_GIF) {
            imagegif($targetResource, $tempTargetFileName);
        } else {
            throw new CodeHuiterRuntimeException(sprintf('Unsupported target format %d', $targetFormat));
        }
        imagedestroy($sourceResource);
        imagedestroy($targetResource);
        $this->fileStorage->setDefaultChmod($tempTargetFileName);
        $this->fileStorage->copyFile($tempTargetFileName, $target->getBaseDirectory() . $target->getInnerDirectory(), $targetFileName);
        $this->fileStorage->deleteFile($tempTargetFileName);
        return new ImageResizeResult($target->getBaseDirectory(), $relationalTargetFileName, $cropResult);
    }

    /**
     * @param string $file
     * @param int $format
     * @return resource
     */
    private function getSourceResource(string $file, int $format)
    {
        $source = null;
        try {
            switch ($format) {
                case ImageProperties::FORMAT_PNG: $source = imagecreatefrompng($file); break;
                case ImageProperties::FORMAT_JPEG: $source = imagecreatefromjpeg($file); break;
                case ImageProperties::FORMAT_GIF: $source = imagecreatefromgif($file); break;
                default: throw  new CodeHuiterRuntimeException(sprintf('Invalid file format code %d', $format));
            }

        } catch (Exception $exception) {
            throw new CodeHuiterRuntimeException('Cant open file', Response::HTTP_CODE_FORBIDDEN, $exception);
        }
        if (!$source) {
            throw new CodeHuiterRuntimeException('Cant open file wrong source got', Response::HTTP_CODE_FORBIDDEN);
        }
        return $source;
    }

    private function getTargetResource(int $width, int $height)
    {
        $targetResource = null;
        try {
            $targetResource = imagecreatetruecolor($width, $height);
        } catch (Exception $exception) {
            throw new CodeHuiterRuntimeException('Cant create target resource', Response::HTTP_CODE_FORBIDDEN, $exception);
        }
        if (!$targetResource) {
            throw new CodeHuiterRuntimeException('Cant create target resource, false given', Response::HTTP_CODE_FORBIDDEN);
        }
        return $targetResource;
    }

    private function getFrameCoordinates(
        int $resizeType,
        int $originalWidth,
        int $originalHeight,
        int $targetWidth,
        int $targetHeight,
        ?CropOptions $cropOptions
    ): array {
        $sourceFrameWidth = $originalWidth;
        $sourceFrameHeight = $originalHeight;
        $sourceFrameOffsetX = $cropOptions ? $cropOptions->getSourceOffsetX() : null;
        $sourceFrameOffsetY = $cropOptions ? $cropOptions->getSourceOffsetX() : null;
        $targetFrameWidth = 0;
        $targetFrameHeight = 0;

        switch ($resizeType) {
            case ResizeOptions::RESIZE_TYPE_SIMPLE:
                $targetFrameWidth = $targetWidth;
                $targetFrameHeight = $targetHeight;
                break;
            case ResizeOptions::RESIZE_TYPE_WIDTH:
                $targetFrameWidth = $targetWidth;
                $targetFrameHeight = (int)($originalHeight * $targetWidth / $originalWidth);
                break;
            case ResizeOptions::RESIZE_TYPE_HEIGHT:
                $targetFrameWidth = (int)($originalWidth * $targetHeight / $originalHeight);
                $targetFrameHeight = $targetHeight;
                break;
            case ResizeOptions::RESIZE_TYPE_MAXIMUM:
            case ResizeOptions::RESIZE_TYPE_MAXIMUM_WITH_SAVE_SMALL:
                if ((1.0 * $targetWidth / $targetHeight) < (1.0 * $originalWidth / $originalHeight)) {
                    $targetFrameWidth = $targetWidth;
                    $targetFrameHeight = (int)($originalHeight * $targetWidth / $originalWidth);
                } else {
                    $targetFrameWidth = (int)($originalWidth * $targetHeight / $originalHeight);
                    $targetFrameHeight = $targetHeight;
                }
                break;
            case ResizeOptions::RESIZE_TYPE_CROP:
                // get sourceFrame proportional equal targetFrame
                $targetFrameWidth = $targetWidth;
                $targetFrameHeight = $targetHeight;
                if ((1.0 * $targetWidth / $targetHeight) > (1.0 * $originalWidth / $originalHeight)) {
                    $sourceFrameWidth = $originalWidth;
                    $sourceFrameHeight = (int)($targetHeight * $originalWidth / $targetWidth);
                    if ($sourceFrameOffsetY === null) {
                        $sourceFrameOffsetY = IntModifier::normalizeBetween((int)(($originalHeight - $sourceFrameHeight) / 2), 0);
                    }
                } else {
                    $sourceFrameWidth = (int)($targetWidth * $originalHeight / $targetHeight);
                    $sourceFrameHeight = $originalHeight;
                    if ($sourceFrameOffsetX === null) {
                        $sourceFrameOffsetX = IntModifier::normalizeBetween((int)(($originalWidth - $sourceFrameWidth) / 2), 0);
                    }
                }
                break;
        }
        if ($cropOptions && $cropOptions->getSourceWidthCrop() && $cropOptions->getSourceHeightCrop()) {
            $sourceFrameWidth = $cropOptions->getSourceWidthCrop();
            $sourceFrameHeight = $cropOptions->getSourceHeightCrop();
        }
        if ($sourceFrameOffsetX === null) {
            $sourceFrameOffsetX = 0;
        }
        if ($sourceFrameOffsetY === null) {
            $sourceFrameOffsetY = 0;
        }

        if (!$targetFrameHeight || !$targetFrameWidth) {
            throw new CodeHuiterRuntimeException(sprintf('Target width = %d or height %d is equal zero', $targetWidth, $targetHeight));
        }

        return [
            'originalWidth' => $originalWidth,
            'originalHeight' => $originalHeight,
            'sourceFrameWidth' => $sourceFrameWidth,
            'sourceFrameHeight' => $sourceFrameHeight,
            'sourceFrameOffsetX' => $sourceFrameOffsetX,
            'sourceFrameOffsetY' => $sourceFrameOffsetY,
            'targetFrameWidth' => $targetFrameWidth,
            'targetFrameHeight' => $targetFrameHeight,
        ];
    }

    private function getDestinationFileName(string $originalFile, string $fileTitle, int $format): string
    {
        $newName = $fileTitle;
        if (strpos($newName,'{#rand}') !== false){
            $newName = StringModifier::replace($newName, [
                '{#rand}' => md5(filesize($originalFile) . IntModifier::random(0, 10000)),
            ]);
        }
        if (strpos($newName,'{#rand6}') !== false){
            $newName = StringModifier::replace($newName, [
                '{#rand6}' => StringModifier::sub(md5(filesize($originalFile) . IntModifier::random(0, 10000)), 0, 6),
            ]);
        }
        if (strpos($newName,'{#timestamp}') !== false){
            $newName = StringModifier::replace($newName, [
                '{#timestamp}' => $this->dateService->getCurrentDateTime()->getTimestamp(),
            ]);
        }
        if (strpos($newName,'{#date}') !== false){
            $newName = StringModifier::replace($newName, [
                '{#date}' => $this->dateService->getCurrentDateTime()->format('Ymd'),
            ]);
        }
        if (strpos($newName,'{#time}') !== false){
            $newName = StringModifier::replace($newName, [
                '{#time}' => $this->dateService->getCurrentDateTime()->format('His'),
            ]);
        }

        $extMap = [
            ImageProperties::FORMAT_PNG => 'png',
            ImageProperties::FORMAT_JPEG => 'jpg',
            ImageProperties::FORMAT_GIF => 'gif',
        ];
        $newName .= isset($extMap[$format]) ? '.' . $extMap[$format] : '';
        return $newName;
    }

    private function getImageProperties(string $file): ImageProperties
    {
        try {
            return ImageProperties::fromFile($file);
        } catch (Exception $exception) {
            $this->logger->withTag(self::LOG_TAG)->notice(
                sprintf('Invalid image file was uploaded [%s]: %s', $file, $exception->getMessage())
            );
            throw new CodeHuiterRuntimeException('Invalid file', Response::HTTP_CODE_FORBIDDEN, $exception);
        }
    }
}

<?php

namespace CodeHuiter\Pattern\Module\Media;

use CodeHuiter\Config\ConnectorConfig;
use CodeHuiter\Core\Application;
use CodeHuiter\Modifier\StringModifier;
use CodeHuiter\Pattern\Module\Auth\AuthService;
use CodeHuiter\Pattern\Module\Auth\Model\User;
use CodeHuiter\Pattern\Module\Auth\Model\UserRepository;
use CodeHuiter\Pattern\Module\Connector\ConnectableObject;
use CodeHuiter\Pattern\Module\Media\Image\ExifReader;
use CodeHuiter\Pattern\Module\Media\Image\ImageProcessor;
use CodeHuiter\Pattern\Module\Media\Image\Options\DestinationOptions;
use CodeHuiter\Pattern\Module\Media\Image\Options\ResizeOptions;
use CodeHuiter\Pattern\Module\Media\Image\Options\WatermarkImageOptions;
use CodeHuiter\Pattern\Module\Media\Image\Options\WatermarkOptions;
use CodeHuiter\Pattern\Module\Media\Model\Media;
use CodeHuiter\Pattern\Module\Media\Model\MediaModel;
use CodeHuiter\Pattern\Module\Media\Model\MediaRepository;
use CodeHuiter\Pattern\Result\ModuleResult;
use CodeHuiter\Pattern\Service\Content;
use CodeHuiter\Service\DateService;
use CodeHuiter\Service\EventDispatcher;
use CodeHuiter\Service\FileStorage;
use CodeHuiter\Service\Language;
use CodeHuiter\Service\Logger;

class MediaService
{
    private const PHOTO_CONTENT = 'user_medias';

    /**
     * @var Application
     */
    private $application;

    /**
     * @var ImageProcessor|null
     */
    private $imageProcessor = null;

    /**
     * @param Application $application
     */
    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    public function connectMedia(Media $media, string $objectType, string $objectId): void
    {
        $media->setObjectType($objectType);
        $media->setObjectId($objectId);
    }

    public function updateUserPhoto(User $user, ?Media $media = null): void
    {
        $setUserMedia = null;
        if ($media && $media->getType() === Media::TYPE_PHOTO && $media->getUserId() === $user->getId()) {
            $setUserMedia = $media;
        }
        if (!$setUserMedia && $user->getPictureId()) {
            // We can set user photo from settled ID
            $setUserMedia = $this->getMediaRepository()->findOne([
                MediaModel::FIELD_ID => $setUserMedia->getId(),
                MediaModel::FIELD_USER_ID => $user->getId(),
                MediaModel::FIELD_TYPE => Media::TYPE_PHOTO,
            ]);
        }
        if (!$setUserMedia) {
            // We can set last user photo
            $setUserMedia = $this->getMediaRepository()->findOne([
                MediaModel::FIELD_ID => $setUserMedia->getId(),
                MediaModel::FIELD_USER_ID => $user->getId(),
                MediaModel::FIELD_TYPE => Media::TYPE_PHOTO,
            ], ['order' => [MediaModel::FIELD_SORT_NUMBER => 'desc']]);
        }
        if (!$user->isInGroup(AuthService::GROUP_NOT_DELETED)) {
            $user->setPictureId(0);
            $user->setPictureOrig('default/profile_unactive.png');
            $user->setPicture('default/profile_unactive.png');
            $user->setPicturePreview('default/profile_unactive_preview.png');
            $this->getUserRepository()->save($user);
            return;
        }
        if (!$user->isInGroup(AuthService::GROUP_NOT_BANNED)) {
            $user->setPictureId(0);
            $user->setPictureOrig('default/profile_banned.png');
            $user->setPicture('default/profile_banned.png');
            $user->setPicturePreview('default/profile_banned_preview.png');
            $this->getUserRepository()->save($user);
            return;
        }
        if ($setUserMedia) {
            $user->setPictureId($setUserMedia->getId());
            $user->setPictureOrig($setUserMedia->getPictureOrig());
            $user->setPicture($setUserMedia->getPicture());
            $user->setPicturePreview($setUserMedia->getPicturePreview());
            $this->getUserRepository()->save($user);
            return;
        }
        $user->setPictureId(0);
        $user->setPictureOrig('default/profile_nopicture.png');
        $user->setPicture('default/profile_nopicture.png');
        $user->setPicturePreview('default/profile_nopicture_preview.png');
        $this->getUserRepository()->save($user);
    }

    /**
     * @param User $user
     * @param string $tempFile
     * @param ConnectableObject|null $object
     * @param string $name
     * @return ModuleResult $result
     */
    public function addPhoto(User $user, string $tempFile, ?ConnectableObject $object = null, ?string $name = ''): ModuleResult
    {
        if (!file_exists($tempFile)) return ModuleResult::createError($this->getLanguage()->get('media:uploaded_file_not_found'));
        if (!is_file($tempFile)) return ModuleResult::createError($this->getLanguage()->get('media:uploaded_file_is_not_a_file'));
        if (!filesize($tempFile)) return ModuleResult::createError($this->getLanguage()->get('media:uploaded_file_is_empty'));

        $objectType = ($object) ? $object->getConnectorType() : ConnectorConfig::TYPE_TEMP;
        $objectId = ($object) ? $object->getConnectorTypedId() : '';

        $exifData = $this->getExifReader()->get($tempFile);

        $content = $this->getContent();
        $dateTime = $this->getDateService()->getCurrentDateTime();

        $imageProcessor = $this->getImageProcessor();

        $bigResult = $imageProcessor->resize(
            $tempFile,
            new DestinationOptions(
                $content->serverStore(self::PHOTO_CONTENT, ''),
                'dynamic/' . $dateTime->format('Y-m'),
                'm' . $user->getId() . '-{#date}{#time}-big-{#rand6}',
                DestinationOptions::FORMAT_JPEG
            ),
            new ResizeOptions(
                2560, 1440,
                ResizeOptions::RESIZE_TYPE_MAXIMUM_WITH_SAVE_SMALL, 85, false,
                null, null
            )
        );
        $mediumResult = $imageProcessor->resize(
            $tempFile,
            new DestinationOptions(
                $content->serverStore(self::PHOTO_CONTENT, ''),
                'dynamic/' . $dateTime->format('Y-m'),
                'm' . $user->getId() . '-{#date}{#time}-medium-{#rand6}',
                DestinationOptions::FORMAT_JPEG
            ),
            new ResizeOptions(
                1280, 720,
                ResizeOptions::RESIZE_TYPE_MAXIMUM_WITH_SAVE_SMALL, 85, false,
                null, $this->getWatermarkConfig()
            )
        );
        $previewResult = $imageProcessor->resize(
            $content->serverStore(self::PHOTO_CONTENT, $bigResult->getRelationFile()),
            new DestinationOptions(
                $content->serverStore(self::PHOTO_CONTENT, ''),
                'dynamic/' . $dateTime->format('Y-m'),
                'm' . $user->getId() . '-{#date}{#time}-preview-{#rand6}',
                DestinationOptions::FORMAT_JPEG
            ),
            new ResizeOptions(
                300, 300,
                ResizeOptions::RESIZE_TYPE_CROP, 85, false,
                null, null
            )
        );
        $totalSize = filesize($content->serverStore(self::PHOTO_CONTENT, $bigResult->getRelationFile()))
                    + filesize($content->serverStore(self::PHOTO_CONTENT, $mediumResult->getRelationFile()))
                    + filesize($content->serverStore(self::PHOTO_CONTENT, $previewResult->getRelationFile()));

        $mediaRepository = $this->getMediaRepository();
        $media = $mediaRepository->newInstance();

        $media->setActive(true);
        $media->setType('photo');
        $media->setUserId($user->getId());
        $media->setObjectType($objectType);
        $media->setObjectId($objectId);
        $media->setTitle($name);
        $media->setPictureOrig($bigResult->getRelationFile());
        $media->setPicture($mediumResult->getRelationFile());
        $media->setPicturePreview($previewResult->getRelationFile());
        $media->setPreviewParams(StringModifier::jsonEncode($previewResult->getCropResult()));
        $media->setPictureData(StringModifier::jsonEncode($exifData));
        $media->setSortnum($dateTime->getTimestamp());
        $media->setContentSize($totalSize / 1024);

        $mediaRepository->save($media);

        return ModuleResult::createSpecific('media:success_added', ['media' => $media]);
    }

    private function getExifReader(): ExifReader
    {
        return new ExifReader($this->application->get(Logger::class));
    }

    private function getImageProcessor(): ImageProcessor
    {
        if ($this->imageProcessor === null) {
            $this->imageProcessor = new ImageProcessor(
                $this->application->get(FileStorage::class),
                $this->application->get(DateService::class),
                $this->application->get(Logger::class)
            );
        }
        return $this->imageProcessor;
    }

    private function getLanguage(): Language
    {
        return $this->application->get(Language::class);
    }

    private function getDateService(): DateService
    {
        return $this->application->get(DateService::class);
    }

    private function getMediaRepository(): MediaRepository
    {
        return $this->application->get(MediaRepository::class);
    }

    private function getUserRepository(): UserRepository
    {
        return $this->application->get(UserRepository::class);
    }

    private function getContent(): Content
    {
        return $this->application->get(Content::class);
    }

    private function getEventDispatcher(): EventDispatcher
    {
        return $this->application->get(EventDispatcher::class);
    }

    private function getWatermarkConfig(): ?WatermarkOptions
    {
        $watermarkConfig = $this->application->config->mediaConfig->watermark;
        if (isset($watermarkConfig['png'])) {
            return new WatermarkImageOptions(
                $watermarkConfig['png'],
                $watermarkConfig['png_percent'] ?? 10,
                $watermarkConfig['png_x_position'] ?? 'left',
                $watermarkConfig['png_y_position'] ?? 'top'
            );
        }
        return null;
    }
}


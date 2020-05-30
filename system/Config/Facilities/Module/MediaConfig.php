<?php

namespace CodeHuiter\Config\Facilities\Module;

use CodeHuiter\Config\Core\ServiceConfig;
use CodeHuiter\Config\Database\DatabaseConfig;
use CodeHuiter\Config\FacilitiesConfig;
use CodeHuiter\Config\Service\EventsConfig;
use CodeHuiter\Core\Application;
use CodeHuiter\Facilities\Module\Auth\Model\User;
use CodeHuiter\Facilities\Module\Media\Event\MediaSubscriber;
use CodeHuiter\Facilities\Module\Media\MediaService;
use CodeHuiter\Facilities\Module\Media\Model\Media;
use CodeHuiter\Facilities\Module\Media\Model\MediaRepository;

class MediaConfig
{
    public $viewsPath = SYSTEM_PATH . 'Facilities/Module/Media/View/'; // Copy to App Views for custom views

    public $watermark = [
        // Set null if not need watermark
        'png' => 'moponline-water.png',
        'png_percent' => 10,
        'png_x_position' => 'right',
        'png_y_position' => 'bottom',
    ];

    public static function populateConfig(FacilitiesConfig $config): void
    {
        $config->mediaConfig = new self();

        $config->services[MediaService::class] = ServiceConfig::forCallback(
            static function (Application $app) {
                return new MediaService($app);
            }
        );
        $config->services[MediaSubscriber::class] = ServiceConfig::forAppClass(
            MediaSubscriber::class,
            ServiceConfig::SCOPE_REQUEST
        );
        $config->services[MediaRepository::class] = ServiceConfig::forAppClass(
            MediaRepository::class,
            ServiceConfig::SCOPE_REQUEST
        );

        $config->eventsConfig->events[] = [AuthConfig::EVENT_USER_JOIN_ACCOUNT, MediaSubscriber::class];
        $config->eventsConfig->events[] = [EventsConfig::modelDeleting(User::class), MediaSubscriber::class];

        $dbService = DatabaseConfig::SERVICE_DB_DEFAULT;
        $config->databaseConfig->setRelational(Media::class, $dbService, 'media', 'id', ['id']);
    }
}
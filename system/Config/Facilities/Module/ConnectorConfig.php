<?php

namespace CodeHuiter\Config\Facilities\Module;

use CodeHuiter\Config\Core\ServiceConfig;
use CodeHuiter\Config\FacilitiesConfig;
use CodeHuiter\Core\Application;
use CodeHuiter\Facilities\Module;
use CodeHuiter\Facilities\Module\Connector\ConnectAccessibility;
use CodeHuiter\Facilities\Module\Connector\ConnectorService;

class ConnectorConfig
{
    public const TYPE_TEMP = 'temp';
    public const TYPE_PROFILE = 'profile';
    public const TYPE_MEDIA = 'media';

    public const TYPE_PHOTO = 'photo';
    public const TYPE_ALBUM = 'album';

    public $connectObjectRepositories = [
        self::TYPE_PROFILE => Module\Auth\Model\UserRepository::class
    ];

    public static function populateConfig(FacilitiesConfig $config): void
    {
        $config->connectorConfig = new self();
        $config->services[ConnectorService::class] = ServiceConfig::forCallback(
            static function (Application $app) {
                return new ConnectorService($app, $app->config->connectorConfig);
            }
        );
        $config->services[ConnectAccessibility::class] = ServiceConfig::forCallback(
            static function (Application $app) {
                return new ConnectAccessibility($app);
            }
        );
    }
}
<?php

namespace CodeHuiter\Config\Facilities\Module;

use App\Module\ChromeExtension\Model\YoutubeCacheModel;
use CodeHuiter\Config\Core\ServiceConfig;
use CodeHuiter\Config\Database\DatabaseConfig;
use CodeHuiter\Config\FacilitiesConfig;
use CodeHuiter\Facilities\Module\ThirdPartyApi\ThirdPartyApiProvider;

class ThirdPartyApiConfig
{
    public static function populateConfig(FacilitiesConfig $config): void
    {
        $config->services[ThirdPartyApiProvider::class] = ServiceConfig::forAppClass(
            ThirdPartyApiProvider::class,
            ServiceConfig::SCOPE_REQUEST
        );



        $dbService = DatabaseConfig::SERVICE_DB_DEFAULT;
        $config->databaseConfig->setRelational(YoutubeCacheModel::class, $dbService, 'youtube_api_cache', 'id', ['id']);
    }
}
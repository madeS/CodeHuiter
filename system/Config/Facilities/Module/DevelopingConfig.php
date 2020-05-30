<?php

namespace CodeHuiter\Config\Facilities\Module;

use CodeHuiter\Config\Core\ServiceConfig;
use CodeHuiter\Config\FacilitiesConfig;
use CodeHuiter\Facilities\Module\Developing\DevelopingService;

class DevelopingConfig
{
    public static function populateConfig(FacilitiesConfig $config): void
    {
        $config->services[DevelopingService::class] = ServiceConfig::forClass(
            DevelopingService::class,
            ServiceConfig::SCOPE_REQUEST
        );
    }
}
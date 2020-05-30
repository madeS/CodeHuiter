<?php

namespace CodeHuiter\Config\Service;

use CodeHuiter\Config\Core\ServiceConfig;
use CodeHuiter\Config\CoreConfig;
use CodeHuiter\Core\Application;
use CodeHuiter\Service\DateService;

class DateConfig
{
    public $siteTimezone = 'UTC';

    public static function populateConfig(CoreConfig $config): void
    {
        $config->dateConfig = new self();
        $config->services[DateService::class] = ServiceConfig::forCallback(
            static function (Application $app) {
                return new \CodeHuiter\Service\ByDefault\DateService($app->config->dateConfig);
            },
            ServiceConfig::SCOPE_REQUEST
        );
    }
}
<?php

namespace CodeHuiter\Config\Facilities\Service;

use CodeHuiter\Config\Core\ServiceConfig;
use CodeHuiter\Config\FacilitiesConfig;
use CodeHuiter\Core\Application;
use CodeHuiter\Facilities\Service\Validator;
use CodeHuiter\Service\Language;

class ValidatorConfig
{
    public static function populateConfig(FacilitiesConfig $config): void
    {
        $config->services[Validator::class] = ServiceConfig::forCallback(
            static function (Application $app) {
                return new \CodeHuiter\Facilities\Service\ByDefault\Validator($app->get(Language::class));
            }
        );
    }
}
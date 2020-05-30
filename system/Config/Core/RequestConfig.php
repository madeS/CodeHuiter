<?php

namespace CodeHuiter\Config\Core;

use CodeHuiter\Config\CoreConfig;
use CodeHuiter\Core\Application;
use CodeHuiter\Core\Request;
use CodeHuiter\Core\ByDefault;

class RequestConfig
{
    /**
     * Allowed URL Characters
     * @var string
     */
    public $permittedUriChars = 'a-z 0-9~%.:_\-\,';

    public static function populateConfig(CoreConfig $config): void
    {
        $config->requestConfig = new self();
        $config->services[Request::class] = ServiceConfig::forCallback(
            static function (Application $app) {
                return new ByDefault\Request($app->config->requestConfig);
            }
        );
    }
}
<?php

namespace CodeHuiter\Config\Service;

use CodeHuiter\Config\Core\ServiceConfig;
use CodeHuiter\Config\CoreConfig;
use CodeHuiter\Core\Application;
use CodeHuiter\Service\ByDefault\Log\FileLogger;
use CodeHuiter\Service\Logger;

class LoggerConfig
{
    /** @var array|string */
    public $threshold = 'notice';
    public $directory = STORAGE_PATH . 'framework/logs/';
    public $byFile = '{#tag}_{#level}';
    public $datePrepend = 'Y-m';
    public $filePermission = 0777;
    public $dateFormat = 'Y-m-d H:i:s';
    public $defaultLevel = 'debug';

    public static function populateConfig(CoreConfig $config): void
    {
        $config->logConfig = new self();
        $config->services[Logger::class] = ServiceConfig::forCallback(
            static function (Application $app) {
                return new FileLogger($app->config->logConfig);
            }
        );
    }
}
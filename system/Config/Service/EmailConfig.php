<?php

namespace CodeHuiter\Config\Service;

use CodeHuiter\Config\Core\ServiceConfig;
use CodeHuiter\Config\CoreConfig;
use CodeHuiter\Config\Database\DatabaseConfig;
use CodeHuiter\Core\Application;
use CodeHuiter\Service\DateService;
use CodeHuiter\Service\Logger;
use CodeHuiter\Service\Mailer;
use CodeHuiter\Service\ByDefault\Email\Model;
use CodeHuiter\Service\RelationalRepositoryProvider;

class EmailConfig
{
    public $siteRobotEmail = 'robot@app.local';
    public $siteRobotName = 'CodeHuiter Robot Name';
    public $queueForce = false;
    public $senderConfig = [

    ];

    public static function populateConfig(CoreConfig $config): void
    {
        $config->emailConfig = new self();
        $config->services[Mailer::class] = ServiceConfig::forCallback(
            static function (Application $app) {
                return new \CodeHuiter\Service\ByDefault\Email\Mailer(
                    $app->config->emailConfig,
                    $app->get(Logger::class),
                    $app->get(DateService::class),
                    $app->get(RelationalRepositoryProvider::class)
                );
            }
        );

        $db = DatabaseConfig::SERVICE_DB_DEFAULT;
        $config->databaseConfig->setRelational(Model\Mailer::class, $db, 'mailer', 'id', ['id']);
    }
}
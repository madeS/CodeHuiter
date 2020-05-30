<?php

namespace CodeHuiter\Config\Database;

use CodeHuiter\Config\Core\ServiceConfig;
use CodeHuiter\Config\CoreConfig;
use CodeHuiter\Core\Application;
use CodeHuiter\Database\Handlers\PDORelationalDatabaseHandler;
use CodeHuiter\Database\RelationalDatabaseHandler;
use CodeHuiter\Service\RelationalRepositoryProvider;
use CodeHuiter\Service\Logger;

class DatabaseConfig
{
    public const SERVICE_DB_DEFAULT = 'db';

    /**
     * @var ConnectionDatabaseConfig[] <dbHandlerServiceName, ConnectionDatabaseConfig>
     */
    public $connectionConfigs = [];

    /**
     * @var RepositoryConfig[] <ModelClass, RepositoryConfig>
     */
    public $models = [];

    public function setRelational(
        string $model,
        string $dbService,
        string $table,
        string $autoIncrement = 'id',
        array $primaries = ['id']
    ): void {
        $this->models[$model] = new RelationalRepositoryConfig(
            $model,
            $dbService,
            $table,
            $autoIncrement,
            $primaries
        );
    }

    public static function populateConfig(CoreConfig $config): void
    {
        $config->databaseConfig = new self();
        $config->databaseConfig->connectionConfigs[self::SERVICE_DB_DEFAULT] = new ConnectionDatabaseConfig();
        $config->services[self::SERVICE_DB_DEFAULT] = ServiceConfig::forCallback(
            static function (Application $app) {
                return new PDORelationalDatabaseHandler(
                    $app->get(Logger::class),
                    $app->config->databaseConfig->connectionConfigs[self::SERVICE_DB_DEFAULT]
                );
            },
            null,
            RelationalDatabaseHandler::class
        );

        $config->services[RelationalRepositoryProvider::class] = ServiceConfig::forAppClass(
            \CodeHuiter\Service\ByDefault\RelationalRepositoryProvider::class,
            ServiceConfig::SCOPE_REQUEST
        );
    }
}

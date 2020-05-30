<?php

namespace CodeHuiter\Facilities\Module\Developing\Controller;

use CodeHuiter\Exception\CodeHuiterRuntimeException;
use CodeHuiter\Facilities\Controller\Base\BaseController;
use CodeHuiter\Facilities\Module\Developing\DevelopingService;

class Database_Controller extends BaseController
{
    public function index(): void
    {
        echo 'Use save/load methods';
    }

    public function save(): void
    {
        if (!$this->request->isCli()) {
            throw new CodeHuiterRuntimeException('This method runs only by CLI');
        }
        foreach ($this->app->config->databaseConfig->connectionConfigs as $dbService => $connectionConfig) {
            $this->getDevelopingService()->getDatabaseManager()->saveDumpDB($connectionConfig, MIGRATION_PATH . 'database/');
        }
    }

    public function load(): void
    {
        if (!$this->request->isCli()) {
            throw new CodeHuiterRuntimeException('This method runs only by CLI');
        }
        if ($this->app->config->projectConfig->disableDbImport) {
            throw new CodeHuiterRuntimeException('This method disabled by config');
        }
        foreach ($this->app->config->databaseConfig->connectionConfigs as $dbService => $connectionConfig) {
            $this->getDevelopingService()->getDatabaseManager()->loadDumpDB($connectionConfig, MIGRATION_PATH . 'database/');
        }

        echo 'OK';
    }

    private function getDevelopingService(): DevelopingService
    {
        return $this->app->get(DevelopingService::class);
    }
}

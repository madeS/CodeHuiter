<?php

namespace CodeHuiter\Pattern\Module\Developing\Controller;

use CodeHuiter\Exception\CodeHuiterRuntimeException;
use CodeHuiter\Pattern\Controller\Base\BaseController;
use CodeHuiter\Pattern\Module\Developing\DevelopingService;

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
        $this->getDevelopingService()->getDatabaseManager()->saveDumpDB(
            $this->app->config->defaultDatabaseConfig,
            MIGRATION_PATH . 'database/'
        );
    }

    public function load(): void
    {
        if (!$this->request->isCli()) {
            throw new CodeHuiterRuntimeException('This method runs only by CLI');
        }
        if ($this->app->config->projectConfig->disableDbImport) {
            throw new CodeHuiterRuntimeException('This method disabled by config');
        }
        $this->getDevelopingService()->getDatabaseManager()->loadDumpDB(
            $this->app->config->defaultDatabaseConfig,
            MIGRATION_PATH . 'database/'
        );
        echo 'OK';
    }

    private function getDevelopingService(): DevelopingService
    {
        return $this->app->get(DevelopingService::class);
    }
}

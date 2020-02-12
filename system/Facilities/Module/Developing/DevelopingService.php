<?php

namespace CodeHuiter\Facilities\Module\Developing;

use CodeHuiter\Facilities\Module\Developing\Manager\DatabaseManager;

class DevelopingService
{
    /** @var DatabaseManager */
    private $databaseManager;

    /**
     * @return DatabaseManager
     */
    public function getDatabaseManager(): DatabaseManager
    {
        if (!isset($this->databaseManager)) {
            $this->databaseManager = new DatabaseManager();
        }
        return $this->databaseManager;
    }
}
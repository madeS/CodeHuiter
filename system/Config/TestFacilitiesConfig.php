<?php

namespace CodeHuiter\Config;

use CodeHuiter\Config\Database\DatabaseConfig;

class TestFacilitiesConfig extends FacilitiesConfig
{
    public function __construct()
    {
        parent::__construct();

        $this->webConfig->domain = 'app.local';

        $dbName = DatabaseConfig::SERVICE_DB_DEFAULT;
        $this->databaseConfig->connectionConfigs[$dbName]->dsn = 'mysql:host=localhost;dbname=app_test_db';
        $this->databaseConfig->connectionConfigs[$dbName]->username = 'appuser';
        $this->databaseConfig->connectionConfigs[$dbName]->password = 'apppassword';

        $this->authConfig->emailForce = false;
    }
}

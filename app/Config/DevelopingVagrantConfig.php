<?php

namespace App\Config;

use CodeHuiter\Config\Database\DatabaseConfig;

class DevelopingVagrantConfig extends DefaultConfig
{
    public function __construct()
    {
        parent::__construct();

        $this->frameworkConfig->showErrors = true;

        $dbName = DatabaseConfig::SERVICE_DB_DEFAULT;
        $this->databaseConfig->connectionConfigs[$dbName]->dsn = 'mysql:host=localhost;dbname=app_db';
        $this->databaseConfig->connectionConfigs[$dbName]->username = 'appuser';
        $this->databaseConfig->connectionConfigs[$dbName]->password = 'apppassword';

        $this->compressorConfig->version = '20200526224500';
        //$this->compressorConfig->version = 'dev';
    }
}

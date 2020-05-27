<?php

namespace App\Config;

class DevelopingVagrantConfig extends DefaultConfig
{
    public function __construct()
    {
        parent::__construct();

        $this->frameworkConfig->showErrors = true;

        $this->defaultDatabaseConfig->dsn = 'mysql:host=localhost;dbname=app_db';
        $this->defaultDatabaseConfig->username = 'appuser';
        $this->defaultDatabaseConfig->password = 'apppassword';

        $this->compressorConfig->version = '20200526224500';
        //$this->compressorConfig->version = 'dev';
    }
}

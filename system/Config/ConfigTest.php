<?php

namespace CodeHuiter\Config;

class ConfigTest extends FacilitiesConfig
{
    public function __construct()
    {
        parent::__construct();

        $this->settingsConfig->domain = 'app.local';

        $this->defaultDatabaseConfig->dsn = 'mysql:host=localhost;dbname=app_test_db';
        $this->defaultDatabaseConfig->username = 'appuser';
        $this->defaultDatabaseConfig->password = 'apppassword';

        $this->authConfig->emailForce = false;
    }
}

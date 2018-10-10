<?php

namespace App\Config;

class DevelopingVagrantConfig extends DefaultConfig
{
    public function __construct()
    {
        parent::__construct();

        $this->configs[self::CONFIG_KEY_FRAMEWORK]['show_errors'] = true;
        $this->configs['database_default'] = array_merge(
            $this->configs['database_default'],
            [
                'dsn' => 'mysql:host=localhost;dbname=app_db',
                'username' => 'appuser',
                'password' => 'apppassword',
            ]
        );
    }
}

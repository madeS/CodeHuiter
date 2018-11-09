<?php

namespace App\Config;

class DevelopingVagrantConfig extends DefaultConfig
{
    public function __construct()
    {
        parent::__construct();

        $this->configs[self::CONFIG_KEY_FRAMEWORK]['show_errors'] = true;
        $this->configs[self::CONFIG_KEY_DB_DEFAULT] = array_merge(
            $this->configs[self::CONFIG_KEY_DB_DEFAULT],
            [
                'dsn' => 'mysql:host=localhost;dbname=app_db',
                'username' => 'appuser',
                'password' => 'apppassword',
            ]
        );

        //$this->configs[self::SERVICE_KEY_COMPRESSOR]['version'] = '20181109143000';
    }
}

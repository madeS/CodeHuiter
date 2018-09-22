<?php

namespace App\Config;

class DevelopingVagrantConfig extends DefaultConfig
{
    public function __construct()
    {
        parent::__construct();

        $this->config['frameweork']['show_errors'] = true;
        $this->config['database_default'] = array_merge(
            $this->config['database_default'],
            [
                'dsn' => 'mysql:host=localhost;dbname=app_db',
                'username' => 'appuser',
                'password' => 'apppassword',
            ]
        );
    }
}

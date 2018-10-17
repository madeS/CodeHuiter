<?php

namespace App\Config;

class ProductionConfig extends DefaultConfig
{
    public function __construct()
    {
        parent::__construct();

        $this->configs[self::CONFIG_KEY_FRAMEWORK]['show_errors'] = false;
    }

    protected function initErrorReporting()
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 0);
    }
}

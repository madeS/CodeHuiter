<?php

namespace App\Config;

class ProductionConfig extends DefaultConfig
{
    public function __construct()
    {
        parent::__construct();

        $this->config['frameweork']['show_errors'] = false;
//        $this->config['database'] = [
//
//        ];
    }

    protected function initErrorReporting()
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 0);
    }
}

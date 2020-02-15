<?php

namespace App\Config;

use CodeHuiter\Config\FacilitiesConfig;

class DefaultConfig extends FacilitiesConfig
{
    public function __construct()
    {
        parent::__construct();

        $this->routerConfig->routes['extension/(:all)'] = 'APP_MODULE_ChromeExtension/$1';

        $this->settingsConfig->domain = 'app.local';
    }
}

<?php

namespace App\Config;

use CodeHuiter\Config\PatternConfig;

class DefaultConfig extends PatternConfig
{
    public function __construct()
    {
        parent::__construct();

        $this->settingsConfig->domain = 'app.local';
    }
}

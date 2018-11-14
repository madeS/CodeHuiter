<?php

namespace App\Config;

class DefaultConfig extends \CodeHuiter\Config\PatternConfig
{
    public function __construct()
    {
        parent::__construct();

        $this->settingsConfig->domain = 'app.local';
    }
}

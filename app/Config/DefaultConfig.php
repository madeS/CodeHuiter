<?php

namespace App\Config;

class DefaultConfig extends \CodeHuiter\Config\PatternConfig
{
    public function __construct()
    {
        parent::__construct();

        $this->configs['domain'] = [
            'main' => 'app.local',
            'subdomain' => 'sub.app.local',
        ];
    }
}

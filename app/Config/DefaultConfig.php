<?php

namespace App\Config;

class DefaultConfig extends \CodeHuiter\Config\PatternConfig
{
    public function __construct()
    {
        parent::__construct();

        $this->configs[self::CONFIG_KEY_MAIN]['domain'] = 'app.local';
    }
}

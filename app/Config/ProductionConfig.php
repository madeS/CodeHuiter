<?php

namespace App\Config;

class ProductionConfig extends DefaultConfig
{
    public function __construct()
    {
        parent::__construct();

        $this->frameworkConfig->showErrors = false;
    }

}

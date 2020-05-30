<?php

namespace CodeHuiter\Config\Core;

use CodeHuiter\Core\Application;

interface InitializedConfig
{
    public function initialize(Application $application): void;
}
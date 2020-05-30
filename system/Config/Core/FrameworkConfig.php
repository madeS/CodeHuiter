<?php

namespace CodeHuiter\Config\Core;

use CodeHuiter\Config\CoreConfig;
use CodeHuiter\Core\Application;
use CodeHuiter\Core\CodeLoader;

class FrameworkConfig implements InitializedConfig
{
    /** @var bool */
    public $showDebugBacktrace = true;
    /** @var bool */
    public $showErrors = true;

    public static function populateConfig(CoreConfig $config): void
    {
        $config->frameworkConfig = new self();
        $config->services[CodeLoader::class] = ServiceConfig::forClass(CodeLoader::class);


    }

    public function initialize(Application $application): void
    {
        if (!isset($_SERVER['DOCUMENT_ROOT'])) {
            $_SERVER['DOCUMENT_ROOT'] = PUB_PATH;
        }

        if ($this->showErrors) {
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
        } else {
            error_reporting(E_ALL);
            ini_set('display_errors', 0);
        }
    }
}
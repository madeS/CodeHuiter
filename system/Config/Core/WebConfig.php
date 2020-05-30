<?php

namespace CodeHuiter\Config\Core;

use CodeHuiter\Config\CoreConfig;
use CodeHuiter\Core\Application;

class WebConfig implements InitializedConfig
{
    /** @var string */
    public $template = 'default';
    /** @var string */
    public $protocol = 'http';
    /** @var string */
    public $domain = 'app.local';
    /** @var string */
    public $language = 'russian';
    /** @var string */
    public $siteUrl = '';

    public static function populateConfig(CoreConfig $config): void
    {
        $config->webConfig = new self();
    }

    public function initialize(Application $application): void
    {
        $this->siteUrl = $this->protocol . '://' . $this->domain;
    }
}
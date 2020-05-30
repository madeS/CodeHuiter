<?php

namespace CodeHuiter\Config;

use App\Service\Links;
use CodeHuiter\Config;
use CodeHuiter\Config\Facilities\Module\AuthConfig;
use CodeHuiter\Config\Facilities\Module\ConnectorConfig;
use CodeHuiter\Config\Facilities\Module\DevelopingConfig;
use CodeHuiter\Config\Facilities\Module\MediaConfig;
use CodeHuiter\Config\Facilities\Module\ThirdPartyApiConfig;
use CodeHuiter\Config\Facilities\ProjectConfig;
use CodeHuiter\Config\Facilities\Service\AjaxResponseConfig;
use CodeHuiter\Config\Facilities\Service\CompressorConfig;
use CodeHuiter\Config\Facilities\Service\ContentConfig;
use CodeHuiter\Config\Facilities\Service\LinksConfig;
use CodeHuiter\Config\Facilities\Service\ValidatorConfig;
use CodeHuiter\Facilities;
use CodeHuiter\Facilities\Module;

class FacilitiesConfig extends CoreConfig
{
    /**
     * @var Config\Facilities\ProjectConfig
     */
    public $projectConfig;
    /**
     * @var Config\Facilities\Service\LinksConfig
     */
    public $linksConfig;
    /**
     * @var CompressorConfig
     */
    public $compressorConfig;
    /**
     * @var ContentConfig
     */
    public $contentConfig;
    /**
     * @var AuthConfig
     */
    public $authConfig;
    /**
     * @var ConnectorConfig
     */
    public $connectorConfig;
    /**
     * @var MediaConfig
     */
    public $mediaConfig;

    // Injected services into controller
    public const INJECTED_COMPRESSOR = 'compressor';
    public const INJECTED_AJAX_RESPONSE = 'ajaxResponse';
    public const INJECTED_VALIDATOR = 'validator';
    public const INJECTED_LINKS = 'links';
    public const INJECTED_CONTENT = 'content';
    public const INJECTED_AUTH = 'auth';
    public const INJECTED_USER = 'userService';

    public function __construct()
    {
        parent::__construct();

        ProjectConfig::populateConfig($this);
        LinksConfig::populateConfig($this);

        CompressorConfig::populateConfig($this);
        AjaxResponseConfig::populateConfig($this);
        ContentConfig::populateConfig($this);
        ValidatorConfig::populateConfig($this);

        ConnectorConfig::populateConfig($this);
        AuthConfig::populateConfig($this);
        MediaConfig::populateConfig($this);

        ThirdPartyApiConfig::populateConfig($this);
        DevelopingConfig::populateConfig($this);

        /**
         * Injected to controller
         */
        $this->injectedServices[self::INJECTED_COMPRESSOR] = Facilities\Service\Compressor::class;
        $this->injectedServices[self::INJECTED_AJAX_RESPONSE] = Facilities\Service\AjaxResponse::class;
        $this->injectedServices[self::INJECTED_LINKS] = Links::class;
        $this->injectedServices[self::INJECTED_CONTENT] = Facilities\Service\Content::class;
        $this->injectedServices[self::INJECTED_VALIDATOR] = Facilities\Service\Validator::class;
        $this->injectedServices[self::INJECTED_AUTH] = Module\Auth\AuthService::class;
        $this->injectedServices[self::INJECTED_USER] = Module\Auth\UserService::class;

        $this->routerConfig->routes['users/id(:num)'] = 'users/get/$1';
        $this->routerConfig->routes['users/id(:num)/medias'] = 'medias/media_list/$1';
        $this->routerConfig->routes['users/id(:num)/albums'] = 'medias/album_list/$1';
        $this->routerConfig->routes['users/id(:num)/album(:num)'] = 'medias/album_view/$1/$2';
        $this->routerConfig->routes['users/id(:num)/album(:num)/edit'] = 'users/album_edit/$1/$2';
    }
}

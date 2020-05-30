<?php

namespace CodeHuiter\Config;

use CodeHuiter\Config\Core\FrameworkConfig;
use CodeHuiter\Config\Core\InitializedConfig;
use CodeHuiter\Config\Core\RequestConfig;
use CodeHuiter\Config\Core\ResponseConfig;
use CodeHuiter\Config\Core\RouterConfig;
use CodeHuiter\Config\Core\ServiceConfig;
use CodeHuiter\Config\Core\WebConfig;
use CodeHuiter\Config\Database\DatabaseConfig;
use CodeHuiter\Config\Service\DateConfig;
use CodeHuiter\Config\Service\EmailConfig;
use CodeHuiter\Config\Service\EventsConfig;
use CodeHuiter\Config\Service\LoggerConfig;
use CodeHuiter\Config\Service\RendererConfig;
use CodeHuiter\Core\Application;
use CodeHuiter\Core\CodeLoader;
use CodeHuiter\Core\Request;
use CodeHuiter\Core\Response;
use CodeHuiter\Core\Router;
use CodeHuiter\Service\ByDefault;
use CodeHuiter\Service\Console;
use CodeHuiter\Service\DateService;
use CodeHuiter\Service\FileStorage;
use CodeHuiter\Service\HtmlParser;
use CodeHuiter\Service\Language;
use CodeHuiter\Service\Logger;
use CodeHuiter\Service\MimeTypeConverter;
use CodeHuiter\Service\Network;
use CodeHuiter\Service\Renderer;

abstract class CoreConfig
{
    // Injected services into controller
    public const INJECTED_LOADER = 'loader';
    public const INJECTED_LOG = 'log';
    public const INJECTED_CONSOLE = 'console';
    public const INJECTED_DATE = 'date';
    public const INJECTED_NETWORK = 'network';
    public const INJECTED_LANG = 'lang';
    public const INJECTED_RENDERER = 'renderer';
    public const INJECTED_REQUEST = 'request';
    public const INJECTED_RESPONSE = 'response';
    public const INJECTED_ROUTER = 'router';

    /**
     * @var array <fieldKey, ServiceName>
     */
    public $injectedServices = [];

    /**
     * @var ServiceConfig[] <ServiceName, ServiceConfig>
     */
    public $services = [];

    /**
     * @var FrameworkConfig
     */
    public $frameworkConfig;
    /**
     * @var WebConfig
     */
    public $webConfig;
    /**
     * @var RequestConfig
     */
    public $requestConfig;
    /**
     * @var ResponseConfig
     */
    public $responseConfig;
    /**
     * @var RouterConfig
     */
    public $routerConfig;
    /**
     * @var LoggerConfig
     */
    public $logConfig;
    /**
     * @var DateConfig
     */
    public $dateConfig;
    /**
     * @var EmailConfig
     */
    public $emailConfig;
    /**
     * @var RendererConfig
     */
    public $rendererConfig;
    /**
     * @var EventsConfig
     */
    public $eventsConfig;
    /**
     * @var DatabaseConfig
     */
    public $databaseConfig;

    public function __construct()
    {
        FrameworkConfig::populateConfig($this);
        WebConfig::populateConfig($this);
        LoggerConfig::populateConfig($this);
        RequestConfig::populateConfig($this);
        RouterConfig::populateConfig($this);
        ResponseConfig::populateConfig($this);
        DatabaseConfig::populateConfig($this);

        EventsConfig::populateConfig($this);
        RendererConfig::populateConfig($this);
        DateConfig::populateConfig($this);
        EmailConfig::populateConfig($this);
        self::populateOther($this);

        self::injectServicesToController($this);
    }

    /**
     * @param Application $app
     */
    public function initialize(Application $app): void
    {
        foreach ($this as $key => $value) {
            $config = $this->$key;
            if ($config instanceof InitializedConfig) {
                $config->initialize($app);
            }
        }
    }

    public static function populateOther(CoreConfig $config): void
    {
        $config->services[Console::class] = ServiceConfig::forCallback(
            static function (Application $app) {
                return new ByDefault\Console(
                    $app->get(Logger::class)
                );
            }
        );
        $config->services[Network::class] = ServiceConfig::forCallback(
            static function (Application $app) {
                return new ByDefault\Network($app->get(Logger::class));
            }
        );
        $config->services[Language::class] = ServiceConfig::forClass(
            ByDefault\Language::class
        );
        $config->services[HtmlParser::class] = ServiceConfig::forClass(
            ByDefault\HtmlParser\SimpleHtmlDomParser::class
        );
        $config->services[MimeTypeConverter::class] = ServiceConfig::forClass(
            ByDefault\MimeTypeConverter::class
        );
        $config->services[FileStorage::class] = ServiceConfig::forCallback(
            static function (Application $app) {
                return new ByDefault\FileStorage($app->get(Logger::class));
            }
        );
    }

    public static function injectServicesToController(CoreConfig $config): void
    {
        $config->injectedServices[self::INJECTED_LOADER] = CodeLoader::class;
        $config->injectedServices[self::INJECTED_REQUEST] = Request::class;
        $config->injectedServices[self::INJECTED_RESPONSE] = Response::class;
        $config->injectedServices[self::INJECTED_ROUTER] = Router::class;
        $config->injectedServices[self::INJECTED_LOG] = Logger::class;
        $config->injectedServices[self::INJECTED_CONSOLE] = Console::class;
        $config->injectedServices[self::INJECTED_DATE] = DateService::class;
        $config->injectedServices[self::INJECTED_NETWORK] = Network::class;
        $config->injectedServices[self::INJECTED_LANG] = Language::class;
        $config->injectedServices[self::INJECTED_RENDERER] = Renderer::class;
    }
}

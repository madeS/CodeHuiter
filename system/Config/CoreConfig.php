<?php

namespace CodeHuiter\Config;

use CodeHuiter\Core;
use CodeHuiter\Core\Application;
use CodeHuiter\Service\ByDefault;
use CodeHuiter\Service;

abstract class CoreConfig
{
    public const KEY_CLASS_APP = 'class_app';
    public const KEY_CLASS = 'class';
    public const KEY_CALLBACK = 'callback';
    public const KEY_VALIDATE = 'validate';
    public const KEY_SCOPE = 'scope'; // permanent by default

    public const SCOPE_PERMANENT = 'scope_permanent';
    public const SCOPE_REQUEST = 'scope_request';
    public const SCOPE_NO_SHARED = 'scope_no_shared';

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

    // Databases
    public const SERVICE_DB_DEFAULT = 'db';

    /**
     * @var array <fieldKey, ServiceName>
     */
    public $injectedServices = [];

    /**
     * @var array <ServiceName, ServiceDescription>
     */
    public $services = [];

    public $serviceConfigs;

    /** @var SettingsConfig */
    public $settingsConfig;
    /** @var FrameworkConfig */
    public $frameworkConfig;

    /** @var RequestConfig */
    public $requestConfig;
    /** @var ResponseConfig */
    public $responseConfig;
    /** @var RouterConfig */
    public $routerConfig;

    /** @var LogConfig */
    public $logConfig;
    /** @var DateConfig */
    public $dateConfig;
    /** @var EmailConfig */
    public $emailConfig;
    /** @var RendererConfig */
    public $rendererConfig;
    /** @var EventsConfig */
    public $eventsConfig;

    /** @var RelationalDatabaseConfig */
    public $defaultDatabaseConfig;

    public function __construct()
    {
        $this->settingsConfig = new SettingsConfig();
        $this->frameworkConfig = new FrameworkConfig();
        $this->eventsConfig = new EventsConfig();

        $this->logConfig = new LogConfig();
        $this->dateConfig = new DateConfig();
        $this->emailConfig = new EmailConfig();
        $this->rendererConfig = new RendererConfig();
        $this->responseConfig = new ResponseConfig();
        $this->requestConfig = new RequestConfig();
        $this->routerConfig = new RouterConfig();

        /**
         * Services
         */
        $this->services += [
            Core\CodeLoader::class => [self::KEY_CLASS => Core\CodeLoader::class],
            Core\Request::class => [
                self::KEY_CALLBACK => static function (Application $app) {
                    return new Core\ByDefault\Request($app->config->requestConfig);
                }
            ],
            Core\Response::class => [
                self::KEY_CALLBACK => static function (Application $app) {
                    return new Core\ByDefault\Response(
                        $app,
                        $app->config->responseConfig,
                        $app->get(Core\Request::class)
                    );
                },
                self::KEY_SCOPE => self::SCOPE_REQUEST,
            ],
            Core\Router::class => [
                self::KEY_CALLBACK => static function (Application $app) {
                    return new Core\Router(
                        $app,
                        $app->config->routerConfig,
                        $app->get(Service\Logger::class),
                        $app->get(Core\CodeLoader::class)
                    );
                },
                self::KEY_SCOPE => self::SCOPE_REQUEST,
            ],
            Service\Logger::class => [
                self::KEY_CALLBACK => static function (Application $app) {
                    return new Service\ByDefault\Log\FileLogger($app->config->logConfig);
                }
            ],
            Service\Console::class => [
                self::KEY_CALLBACK => static function (Application $app) {
                    return new Service\ByDefault\Console($app->get(Service\Logger::class));
                }
            ],
            Service\DateService::class => [
                self::KEY_CALLBACK => static function (Application $app) {
                    return new Service\ByDefault\DateService($app->config->dateConfig);
                },
                self::KEY_SCOPE => self::SCOPE_REQUEST,
            ],
            Service\Network::class => [
                self::KEY_CALLBACK => static function (Application $app) {
                    return new ByDefault\Network($app->get(Service\Logger::class));
                }
            ],
            Service\Language::class => [self::KEY_CLASS => ByDefault\Language::class],
            Service\HtmlParser::class => [self::KEY_CLASS => ByDefault\HtmlParser\SimpleHtmlDomParser::class],
            Service\MimeTypeConverter::class => [self::KEY_CLASS => ByDefault\MimeTypeConverter::class],
            Service\Mailer::class => [
                self::KEY_CALLBACK => static function (Application $app) {
                    return new ByDefault\Email\Mailer(
                        $app->config->emailConfig,
                        $app->get(Service\Logger::class),
                        $app->get(Service\DateService::class)
                    );
                }
            ],
            Service\Renderer::class => [
                self::KEY_CALLBACK => static function (Application $app) {
                    return $app->get(Service\ByDefault\PhpRenderer::class);
                },
                self::KEY_SCOPE => self::SCOPE_REQUEST,
            ],
            Service\ByDefault\PhpRenderer::class => [
                self::KEY_CALLBACK => static function (Application $app) {
                    return new Service\ByDefault\PhpRenderer(
                        $app->config->rendererConfig,
                        $app->get(Core\Response::class),
                        $app->get(Service\Logger::class)
                    );
                },
                self::KEY_SCOPE => self::SCOPE_REQUEST,
            ],
            Service\FileStorage::class => [
                self::KEY_CALLBACK => static function (Application $app) {
                    return new ByDefault\FileStorage($app->get(Service\Logger::class));
                }
            ],
            Service\EventDispatcher::class => [
                self::KEY_CLASS_APP => ByDefault\EventDispatcher\EventDispatcher::class,
            ]
        ];

        $this->injectedServices[self::INJECTED_LOADER] = Core\CodeLoader::class;
        $this->injectedServices[self::INJECTED_REQUEST] = Core\Request::class;
        $this->injectedServices[self::INJECTED_RESPONSE] = Core\Response::class;
        $this->injectedServices[self::INJECTED_ROUTER] = Core\Router::class;
        $this->injectedServices[self::INJECTED_LOG] = Service\Logger::class;
        $this->injectedServices[self::INJECTED_CONSOLE] = Service\Console::class;
        $this->injectedServices[self::INJECTED_DATE] = Service\DateService::class;
        $this->injectedServices[self::INJECTED_NETWORK] = Service\Network::class;
        $this->injectedServices[self::INJECTED_LANG] = Service\Language::class;
        $this->injectedServices[self::INJECTED_RENDERER] = Service\Renderer::class;
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
}

interface InitializedConfig
{
    public function initialize(Application $application): void;
}

class FrameworkConfig implements InitializedConfig
{
    /** @var bool */
    public $showDebugBacktrace = true;
    /** @var bool */
    public $showErrors = true;

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

class SettingsConfig implements InitializedConfig
{
    /** @var string  */
    public $template = 'default';
    /** @var string  */
    public $protocol = 'http';
    /** @var string  */
    public $domain = 'app.local';
    /** @var string  */
    public $language = 'russian';
    /** @var string */
    public $siteUrl = '';

    public function initialize(Application $application): void
    {
        $this->siteUrl = $this->protocol . '://' .  $this->domain;
    }
}

class RendererConfig
{
    /** @var string  */
    public $templateNameAppend = '.tpl.php';
}

class RequestConfig
{
    /**
     * Allowed URL Characters
     * @var string
     */
    public $permittedUriChars = 'a-z 0-9~%.:_\-\,';
}

class ResponseConfig
{
    /** @var string  */
    public $charset = 'UTF-8'; // Recommended
    /**
     * Placeholders:
     *   {#result_time_table}
     *   {#result_class_table}
     *   {#result_time}
     *   {#result_memory}
     * @var bool
     */
    public $profiler = true;
}

class RouterConfig
{
    public $error403 = ['controller' => '\\App\\Controller\\Error\\Error_Controller', 'controller_method' => 'error403'];
    public $error404 = ['controller' => '\\App\\Controller\\Error\\Error_Controller', 'controller_method' => 'error404'];
    public $error500 = ['controller' => '\\App\\Controller\\Error\\Error_Controller', 'controller_method' => 'error500'];
    public $translateUri = [
        '-' => '_',
        '.' => '_dot_',
    ];
    public $translateUriDashes = false;
    public $domainRoutes = [
        'all' => [
            'developing' => 'SYS_MODULE_PATH_Facilities_Module_Developing',
            'developing/(:all)' => 'SYS_MODULE_PATH_Facilities_Module_Developing/$1',
        ],
        'sub.example.com' => [
            //'testmodule/(:all)' => 'APP_MODULE_TestModule/$1',
        ]
    ];
    public $routes = [
        'testmodule/(:all)' => 'APP_MODULE_TestModule/$1',
        'page/(:any)' => "page/get/$1",
        'madm' => 'madm/madm',
        'admin' => 'madm/madm',


        //'users/settings' => 'users/settings',
        'messages/user(:num)' => 'messages/room_by_user/$1',
        'messages/room(:num)' => 'messages/room/$1',

        'page-(:any)/(:any)' => 'blog/get/$2',
        'page-(:any)' => 'blog/get/$1',
        'search' => 'blog/search',
    ];
}

class LogConfig
{
    /** @var array|string */
    public $threshold = 'notice';
    public $directory = STORAGE_PATH . 'framework/logs/';
    public $byFile = '{#tag}_{#level}';
    public $datePrepend = 'Y-m';
    public $filePermission = 0777;
    public $dateFormat = 'Y-m-d H:i:s';
    public $defaultLevel = 'debug';
}

class EmailConfig
{
    public $siteRobotEmail = 'robot@app.local';
    public $siteRobotName = 'CodeHuiter Robot Name';
    public $queueForce = false;
    public $senderConfig = [

    ];
}

class DateConfig
{
    public $siteTimezone = 'UTC';
}

class EventsConfig
{
    public $events = [];

    public static function modelUpdated(string $class)
    {
        return $class . '.updated';
    }

    public static function modelDeleting(string $class)
    {
        return $class . '.deleting';
    }
}

class RelationalDatabaseConfig
{
    public $dsn = 'mysql:host=localhost;dbname=app_db;charset=utf8mb4';
    public $persistent = true;
    public $username = 'appuser';
    public $password = 'apppassword';
    public $charset = 'utf8mb4';
    public $collate = 'utf8mb4_general_ci';
    public $debug = true; // Save in memory data of time executing for totally print page
    public $logIfLonger = 10; // Logging queries if execute time longer than X ms
    public $logTrace = true;
    public $reconnect = false;
}

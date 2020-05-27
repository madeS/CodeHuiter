<?php

namespace CodeHuiter\Config;

use CodeHuiter\Service\EventDispatcher;
use CodeHuiter\Service\FileStorage;
use CodeHuiter\Service\MimeTypeConverter;
use CodeHuiter\Core\Application;
use CodeHuiter\Core\CodeLoader;
use CodeHuiter\Core\Response;
use CodeHuiter\Service\Console;
use CodeHuiter\Service\Logger;
use CodeHuiter\Core\Request;
use CodeHuiter\Service\Renderer;
use CodeHuiter\Core\Router;
use CodeHuiter\Database\RelationalDatabaseHandler;
use CodeHuiter\Database\Handlers\PDORelationalDatabaseHandler;
use CodeHuiter\Service\ByDefault;
use CodeHuiter\Service\DateService;
use CodeHuiter\Service\Mailer;
use CodeHuiter\Service\HtmlParser;
use CodeHuiter\Service\Language;
use CodeHuiter\Service\ByDefault\Log\FileLogger;
use CodeHuiter\Service\Network;

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

    /** @var SettingsConfig */
    public $settingsConfig;
    /** @var FrameworkConfig */
    public $frameworkConfig;

    /** @var LogConfig */
    public $logConfig;
    /** @var DateConfig */
    public $dateConfig;
    /** @var EmailConfig */
    public $emailConfig;
    /** @var RendererConfig */
    public $rendererConfig;
    /** @var RequestConfig */
    public $requestConfig;
    /** @var ResponseConfig */
    public $responseConfig;
    /** @var RouterConfig */
    public $routerConfig;
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
        $this->services[CodeLoader::class] = [self::KEY_CLASS => CodeLoader::class];
        $this->services[Logger::class] = [self::KEY_CALLBACK => static function (Application $app) {
            return new FileLogger($app->config->logConfig);
        }];
        $this->services[Console::class] = [self::KEY_CALLBACK => static function (Application $app) {
            return new ByDefault\Console($app->get(Logger::class));
        }];
        $this->services[DateService::class] = [
            self::KEY_CALLBACK => static function (Application $app) {
                return new ByDefault\DateService($app->config->dateConfig);
            },
            self::KEY_SCOPE => self::SCOPE_REQUEST,
        ];
        $this->services[Network::class] = [self::KEY_CALLBACK => static function (Application $app) {
            return new ByDefault\Network($app->get(Logger::class));
        }];
        $this->services[Language::class] = [self::KEY_CLASS => ByDefault\Language::class];
        $this->services[HtmlParser::class] = [self::KEY_CLASS => ByDefault\HtmlParser\SimpleHtmlDomParser::class];
        $this->services[MimeTypeConverter::class] = [self::KEY_CLASS => ByDefault\MimeTypeConverter::class];
        $this->services[Mailer::class] = [self::KEY_CALLBACK => static function (Application $app) {
            return new ByDefault\Email\Mailer($app->config->emailConfig, $app->get(Logger::class), $app->get(DateService::class));
        }];
        $this->services[Renderer::class] = [
            self::KEY_CALLBACK => static function (Application $app) {
                return $app->get(ByDefault\PhpRenderer::class);
            },
            self::KEY_SCOPE => self::SCOPE_REQUEST,
        ];
        $this->services[ByDefault\PhpRenderer::class] = [
            self::KEY_CALLBACK => static function (Application $app) {
                return new ByDefault\PhpRenderer($app->config->rendererConfig, $app->get(Response::class), $app->get(Logger::class));
            },
            self::KEY_SCOPE => self::SCOPE_REQUEST,
        ];
        $this->services[Request::class] = [self::KEY_CALLBACK => static function (Application $app) {
            return new \CodeHuiter\Core\ByDefault\Request($app->config->requestConfig);
        }];
        $this->services[Response::class] = [
            self::KEY_CALLBACK => static function (Application $app) {
                return new \CodeHuiter\Core\ByDefault\Response($app, $app->config->responseConfig, $app->get(Request::class));
            },
            self::KEY_SCOPE => self::SCOPE_REQUEST,
        ];
        $this->services[Router::class] = [
            self::KEY_CALLBACK => static function (Application $app) {
                return new Router(
                    $app,
                    $app->config->routerConfig,
                    $app->get(Logger::class),
                    $app->get(CodeLoader::class)
                );
            },
            self::KEY_SCOPE => self::SCOPE_REQUEST,
        ];
        $this->services[FileStorage::class] = [
            self::KEY_CALLBACK => static function (Application $app) {
                return new ByDefault\FileStorage($app->get(Logger::class));
            }
        ];
        $this->services[EventDispatcher::class] = [
            self::KEY_CLASS_APP => ByDefault\EventDispatcher\EventDispatcher::class,
        ];

        $this->injectedServices[self::INJECTED_LOADER] = CodeLoader::class;
        $this->injectedServices[self::INJECTED_LOG] = Logger::class;
        $this->injectedServices[self::INJECTED_CONSOLE] = Console::class;
        $this->injectedServices[self::INJECTED_DATE] = DateService::class;
        $this->injectedServices[self::INJECTED_NETWORK] = Network::class;
        $this->injectedServices[self::INJECTED_LANG] = Language::class;
        $this->injectedServices[self::INJECTED_RENDERER] = Renderer::class;
        $this->injectedServices[self::INJECTED_REQUEST] = Request::class;
        $this->injectedServices[self::INJECTED_RESPONSE] = Response::class;
        $this->injectedServices[self::INJECTED_ROUTER] = Router::class;

        $this->services[self::SERVICE_DB_DEFAULT] = [
            self::KEY_CALLBACK => static function (Application $app) {
                return new PDORelationalDatabaseHandler($app->get(Logger::class), $app->config->defaultDatabaseConfig);
            },
            self::KEY_VALIDATE => RelationalDatabaseHandler::class,
        ];
        $this->defaultDatabaseConfig = new RelationalDatabaseConfig();
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

class FrameworkConfig implements InitializedConfig
{
    /** @var bool  */
    public $showDebugBacktrace = true;
    /** @var bool  */
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

    public static function modelUpdatedName(string $class)
    {
        return $class . '.updated';
    }

    public static function modelDeletingName(string $class)
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

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
use CodeHuiter\Database\RelationalDatabase;
use CodeHuiter\Database\Drivers\PDODriver;
use CodeHuiter\Service\ByDefault;
use CodeHuiter\Service\DateService;
use CodeHuiter\Service\Mailer;
use CodeHuiter\Service\HtmlParser;
use CodeHuiter\Service\Language;
use CodeHuiter\Service\ByDefault\Log\FileLog;
use CodeHuiter\Service\Network;

abstract class Config
{
    public const OPT_KEY_SINGLE = 'single';

    public const OPT_KEY_CLASS_APP = 'class_app';
    public const OPT_KEY_CLASS = 'class';
    public const OPT_KEY_CALLBACK = 'callback';
    public const OPT_KEY_VALIDATE = 'validate';
    public const OPT_KEY_SCOPE = 'scope';
    public const OPT_KEY_SCOPE_PERMANENT = 'scope_permanent';
    public const OPT_KEY_SCOPE_REQUEST = 'scope_request';
    public const OPT_KEY_SCOPE_NEW = 'scope_new';

    // TODO Check usages of KEYS
    public const SERVICE_KEY_LOADER = 'loader';
    public const SERVICE_KEY_LOG = 'log';
    public const SERVICE_KEY_CONSOLE = 'console';
    public const SERVICE_KEY_DEBUG = 'debug';
    public const SERVICE_KEY_DATE = 'date';
    public const SERVICE_KEY_NETWORK = 'network';
    public const SERVICE_KEY_LANG = 'lang';
    public const SERVICE_KEY_HTML_PARSER = 'htmlParser';
    public const SERVICE_KEY_EMAIL = 'email';
    public const SERVICE_KEY_RENDERER = 'renderer';
    public const SERVICE_KEY_REQUEST = 'request';
    public const SERVICE_KEY_RESPONSE = 'response';
    public const SERVICE_KEY_ROUTER = 'router';
    public const SERVICE_KEY_DB_DEFAULT = 'db';

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

        /**
         * Class Loader service
         */
        $this->services[CodeLoader::class] = [self::OPT_KEY_CLASS => CodeLoader::class];
        $this->injectedServices[self::SERVICE_KEY_LOADER] = CodeLoader::class;

        /**
         * Logger service
         */
        $this->services[Logger::class] = [self::OPT_KEY_CALLBACK => static function (Application $app) {
            return new FileLog($app->config->logConfig);
        }];
        $this->injectedServices[self::SERVICE_KEY_LOG] = Logger::class;
        $this->logConfig = new LogConfig();

        /**
         * Console Service
         */
        $this->services[Console::class] = [self::OPT_KEY_CALLBACK => static function (Application $app) {
            return new ByDefault\Console($app->get(Logger::class));
        }];
        $this->injectedServices[self::SERVICE_KEY_CONSOLE] = Console::class;

        /**
         * Date Service
         */
        $this->services[DateService::class] = [
            self::OPT_KEY_CALLBACK => static function (Application $app) {
                return new ByDefault\DateService($app->config->dateConfig);
            },
            self::OPT_KEY_SCOPE => self::OPT_KEY_SCOPE_REQUEST,
        ];
        $this->injectedServices[self::SERVICE_KEY_DATE] = DateService::class;
        $this->dateConfig = new DateConfig();

        /**
         * Network Service
         */
        $this->services[Network::class] = [self::OPT_KEY_CALLBACK => static function (Application $app) {
            return new ByDefault\Network($app->get(Logger::class));
        }];
        $this->injectedServices[self::SERVICE_KEY_NETWORK] = Network::class;

        /**
         * Language Service
         */
        $this->services[Language::class] = [self::OPT_KEY_CLASS => ByDefault\Language::class];
        $this->injectedServices[self::SERVICE_KEY_LANG] = Language::class;

        /**
         * HtmlParser Service
         */
        $this->services[HtmlParser::class] = [self::OPT_KEY_CLASS => ByDefault\HtmlParser\SimpleHtmlDomParser::class];
        $this->injectedServices[self::SERVICE_KEY_HTML_PARSER] = HtmlParser::class;

        /**
         * MimeTypeConverter Service
         */
        $this->services[MimeTypeConverter::class] = [self::OPT_KEY_CLASS => ByDefault\MimeTypeConverter::class];

        /**
         * Mailer Service
         */
        $this->services[Mailer::class] = [self::OPT_KEY_CALLBACK => static function (Application $app) {
            return new ByDefault\Email\Mailer($app->config->emailConfig, $app->get(Logger::class), $app->get(DateService::class));
        }];
        $this->injectedServices[self::SERVICE_KEY_EMAIL] = Mailer::class;
        $this->emailConfig = new EmailConfig();

        /**
         * Renderer Service
         */
        $this->services[Renderer::class] = [
            self::OPT_KEY_CALLBACK => static function (Application $app) {
                return $app->get(ByDefault\PhpRenderer::class);
            },
            self::OPT_KEY_SCOPE => self::OPT_KEY_SCOPE_REQUEST,
        ];
        $this->injectedServices[self::SERVICE_KEY_RENDERER] = Renderer::class;
        $this->rendererConfig = new RendererConfig();

        /**
         * PhpRenderer Service
         */
        $this->services[ByDefault\PhpRenderer::class] = [
            self::OPT_KEY_CALLBACK => static function (Application $app) {
                return new ByDefault\PhpRenderer($app->config->rendererConfig, $app->get(Response::class), $app->get(Logger::class));
            },
            self::OPT_KEY_SCOPE => self::OPT_KEY_SCOPE_REQUEST,
        ];

        /**
         * Request Service
         */
        $this->services[Request::class] = [self::OPT_KEY_CALLBACK => static function (Application $app) {
            return new \CodeHuiter\Core\ByDefault\Request($app->config->requestConfig);
        }];
        $this->injectedServices[self::SERVICE_KEY_REQUEST] = Request::class;
        $this->requestConfig = new RequestConfig();

        /**
         * Response Service
         */
        $this->services[Response::class] = [self::OPT_KEY_CALLBACK => static function (Application $app) {
                return new \CodeHuiter\Core\ByDefault\Response($app, $app->config->responseConfig, $app->get(Request::class));
            },
            self::OPT_KEY_SCOPE => self::OPT_KEY_SCOPE_REQUEST,
        ];
        $this->injectedServices[self::SERVICE_KEY_RESPONSE] = Response::class;
        $this->responseConfig = new ResponseConfig();

        /**
         * Router Service
         */
        $this->services[Router::class] = [
            self::OPT_KEY_CALLBACK => static function (Application $app) {
                return new Router(
                    $app,
                    $app->config->routerConfig,
                    $app->get(Logger::class),
                    $app->get(CodeLoader::class)
                );
            },
            self::OPT_KEY_SCOPE => self::OPT_KEY_SCOPE_REQUEST,
        ];
        $this->injectedServices[self::SERVICE_KEY_ROUTER] = Router::class;
        $this->routerConfig = new RouterConfig();

        /**
         * Request Service
         */
        $this->services[FileStorage::class] = [self::OPT_KEY_CALLBACK => static function (Application $app) {
            return new ByDefault\FileStorage($app->get(Logger::class));
        }];

        /**
         * EventDispatcher Service
         */
        $this->services[EventDispatcher::class] = [
            self::OPT_KEY_CLASS_APP => ByDefault\EventDispatcher\EventDispatcher::class,
        ];

        /**
         * Default Database Service
         */
        $this->services[self::SERVICE_KEY_DB_DEFAULT] = [
            self::OPT_KEY_CALLBACK => static function (Application $app) {
                return new PDODriver($app->get(Logger::class), $app->config->defaultDatabaseConfig);
            },
            self::OPT_KEY_VALIDATE => RelationalDatabase::class,
        ];
        $this->injectedServices[self::SERVICE_KEY_DB_DEFAULT] = self::SERVICE_KEY_DB_DEFAULT;
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
    public $permittedUriChars = 'a-z 0-9~%.:_\-';
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
            'developing' => 'SYS_MODULE_PATH_Pattern_Module_Developing',
            'developing/(:all)' => 'SYS_MODULE_PATH_Pattern_Module_Developing/$1',
        ],
        'sub.example.com' => [
            //'testmodule/(:all)' => 'APP_MODULE_TestModule/$1',
        ]
    ];
    public $routes = [
        'testmodule/(:all)' => 'MODULE_TestModule/$1',
        'page/(:any)' => "page/get/$1",
        'madm' => 'madm/madm',
        'admin' => 'madm/madm',

        'users/id(:num)' => 'users/get/$1',
        'users/id(:num)/medias' => 'users/medias/$1',

        'users/id(:num)/albums' => 'users/user_view_albums/$1',
        'users/id(:num)/album(:num)' => 'users/user_view_album/$1/$2',
        'users/id(:num)/album(:num)/edit' => 'users/user_edit_album/$1/$2',
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

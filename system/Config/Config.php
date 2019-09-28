<?php

namespace CodeHuiter\Config;

use CodeHuiter\Config\Data\MimeTypes;
use CodeHuiter\Core\Application;
use CodeHuiter\Core\CodeLoader;
use CodeHuiter\Exception\ServerConfigException;
use CodeHuiter\Service\Console;
use CodeHuiter\Service\Logger;
use CodeHuiter\Core\Request;
use CodeHuiter\Core\Response;
use CodeHuiter\Core\Router;
use CodeHuiter\Database\AbstractDatabase;
use CodeHuiter\Database\Drivers\PDODriver;
use CodeHuiter\Exception\InvalidRequestException;
use CodeHuiter\Exception\RuntimeAppContainerException;
use CodeHuiter\Service\ByDefault;
use CodeHuiter\Service\DateService;
use CodeHuiter\Service\Debug;
use CodeHuiter\Service\Mailer;
use CodeHuiter\Service\HtmlParser;
use CodeHuiter\Service\Language;
use CodeHuiter\Service\ByDefault\Log\FileLog;
use CodeHuiter\Service\Network;

abstract class Config
{
    public const OPT_KEY_SINGLE = 'single';
    public const OPT_KEY_CONFIG_METHOD = 'config_method';
    public const OPT_KEY_CONFIG_METHOD_APP = 'config_method_app';
    public const OPT_KEY_CLASS_APP = 'class_app';
    public const OPT_KEY_CLASS = 'class';
    public const OPT_KEY_CALLBACK = 'callback';

    public const SERVICE_KEY_LOG = 'log';
    public const SERVICE_KEY_CONSOLE = 'console';


    public const SERVICE_KEY_REQUEST = 'request';
    public const SERVICE_KEY_RESPONSE = 'response';
    public const SERVICE_KEY_ROUTER = 'router';
    public const SERVICE_KEY_LOADER = 'loader';

    public const SERVICE_KEY_DEBUG = 'debug';
    public const SERVICE_KEY_EMAIL = 'email';
    public const SERVICE_KEY_DATE = 'date';
    public const SERVICE_KEY_LANG = 'lang';
    public const SERVICE_KEY_MIME_TYPES = 'mimeTypes';
    public const SERVICE_KEY_NETWORK = 'network';
    public const SERVICE_KEY_HTML_PARSER = 'htmlParser';
    public const SERVICE_KEY_DB_DEFAULT = 'db';

    /**
     * @var array
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

    /** @var RequestConfig */
    public $requestConfig;
    /** @var ResponseConfig */
    public $responseConfig;
    /** @var RouterConfig */
    public $routerConfig;

    /** @var DatabaseConfig */
    public $defaultDatabaseConfig;

    public function __construct()
    {
        $this->settingsConfig = new SettingsConfig();
        $this->frameworkConfig = new FrameworkConfig();

        /** @see Config::createServiceLog() */
        $this->services[self::SERVICE_KEY_LOG] = [self::OPT_KEY_CONFIG_METHOD => 'createServiceLog', self::OPT_KEY_SINGLE => true];
        $this->logConfig = new LogConfig();

        /** @see Config::createServiceConsole() */
        $this->services[self::SERVICE_KEY_CONSOLE] = [self::OPT_KEY_CONFIG_METHOD_APP => 'createServiceConsole', self::OPT_KEY_SINGLE => true];

        /** @see Config::createServiceBenchMark() */
        $this->services[self::SERVICE_KEY_LOADER] = [self::OPT_KEY_CONFIG_METHOD => 'createServiceBenchMark', self::OPT_KEY_SINGLE => true];

        /** @see Config::createServiceDate() */
        $this->services[self::SERVICE_KEY_DATE] = [self::OPT_KEY_CONFIG_METHOD => 'createServiceDate', self::OPT_KEY_SINGLE => true];
        $this->dateConfig = new DateConfig();

        /** @see Config::createServiceDebug() */
        $this->services[self::SERVICE_KEY_DEBUG] = [self::OPT_KEY_CONFIG_METHOD => 'createServiceDebug', self::OPT_KEY_SINGLE => true];

        /** @see Config::createServiceNetwork() */
        $this->services[self::SERVICE_KEY_NETWORK] = [self::OPT_KEY_CONFIG_METHOD_APP => 'createServiceNetwork', self::OPT_KEY_SINGLE => true];

        /** @see Config::createServiceMimeTypes() */
        $this->services[self::SERVICE_KEY_MIME_TYPES] = [self::OPT_KEY_CONFIG_METHOD => 'createServiceMimeTypes', self::OPT_KEY_SINGLE => true];

        /** @see Config::createServiceEmail() */
        $this->services[self::SERVICE_KEY_EMAIL] = [self::OPT_KEY_CONFIG_METHOD_APP => 'createServiceEmail', self::OPT_KEY_SINGLE => true];
        $this->emailConfig = new EmailConfig();

        /** @see Config::createServiceLang() */
        $this->services[self::SERVICE_KEY_LANG] = [self::OPT_KEY_CONFIG_METHOD => 'createServiceLang', self::OPT_KEY_SINGLE => true];

        /** @see Config::createServiceParser() */
        $this->services[self::SERVICE_KEY_HTML_PARSER] = [self::OPT_KEY_CONFIG_METHOD => 'createServiceParser', self::OPT_KEY_SINGLE => true];

        /** @see Config::createServiceRequest() */
        $this->services[self::SERVICE_KEY_REQUEST] = [self::OPT_KEY_CONFIG_METHOD => 'createServiceRequest', self::OPT_KEY_SINGLE => true];
        $this->requestConfig = new RequestConfig();

        /** @see Config::createServiceResponse() */
        $this->services[self::SERVICE_KEY_RESPONSE] = [self::OPT_KEY_CONFIG_METHOD_APP => 'createServiceResponse', self::OPT_KEY_SINGLE => true];
        $this->responseConfig = new ResponseConfig();

        /** @see Config::createServiceRouter() */
        $this->services[self::SERVICE_KEY_ROUTER] = [self::OPT_KEY_CONFIG_METHOD_APP => 'createServiceRouter', self::OPT_KEY_SINGLE => true];
        $this->routerConfig = new RouterConfig();

        /** @see Config::createServiceDefaultDB() */
        $this->services[self::SERVICE_KEY_DB_DEFAULT] = [self::OPT_KEY_CONFIG_METHOD_APP => 'createServiceDefaultDB', self::OPT_KEY_SINGLE => true];
        $this->defaultDatabaseConfig = new DatabaseConfig();
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

    /**
     * @return Logger
     */
    public function createServiceLog(): Logger
    {
        return new FileLog($this->logConfig);
    }

    /**
     * @param Application $app
     * @return Console
     * @throws RuntimeAppContainerException
     */
    public function createServiceConsole(Application $app): Console
    {
        return new ByDefault\Console($this->getApplicationServiceLog($app));
    }

    /**
     * @return CodeLoader
     */
    public function createServiceBenchMark(): CodeLoader
    {
        return new CodeLoader();
    }

    /**
     * @return DateService
     */
    public function createServiceDate(): DateService
    {
        return new ByDefault\DateService($this->dateConfig);
    }

    /**
     * @return Debug
     */
    public function createServiceDebug(): Debug
    {
        return new ByDefault\Debug();
    }

    /**
     * @param Application $app
     * @return Network
     * @throws RuntimeAppContainerException
     */
    public function createServiceNetwork(Application $app): Network
    {
        return new ByDefault\Network($this->getApplicationServiceLog($app));
    }

    /**
     * @return MimeTypes
     */
    public function createServiceMimeTypes(): MimeTypes
    {
        return new MimeTypes();
    }

    /**
     * @param Application $app
     * @return Mailer
     * @throws RuntimeAppContainerException
     */
    public function createServiceEmail(Application $app): Mailer
    {
        return new ByDefault\Email\Mailer(
            $this->emailConfig,
            $this->getApplicationServiceLog($app),
            $this->getApplicationServiceDate($app)
        );
    }

    /**
     * @return Language
     */
    public function createServiceLang(): Language
    {
        return new ByDefault\Language();
    }

    /**
     * @return HtmlParser
     */
    public function createServiceParser(): HtmlParser
    {
        return new ByDefault\HtmlParser\SimpleHtmlDomParser();
    }

    /**
     * @return Request
     * @throws InvalidRequestException
     * @throws ServerConfigException
     */
    public function createServiceRequest(): Request
    {
        return new Request($this->requestConfig);
    }

    /**
     * @param Application $app
     * @return Response
     * @throws RuntimeAppContainerException
     */
    public function createServiceResponse(Application $app): Response
    {
        return new Response($app, $this->responseConfig, $this->getApplicationServiceRequest($app));
    }

    /**
     * @param Application $app
     * @return Router
     * @throws RuntimeAppContainerException
     */
    public function createServiceRouter(Application $app) : Router
    {
        return new Router(
            $app,
            $this->routerConfig,
            $this->getApplicationServiceLog($app),
            $this->getApplicationServiceRequest($app),
            $this->getApplicationServiceBenchmark($app)
        );
    }

    /**
     * @param Application $app
     * @return AbstractDatabase
     * @throws RuntimeAppContainerException
     */
    public function createServiceDefaultDB(Application $app): AbstractDatabase
    {
        return new PDODriver(
            $this->getApplicationServiceLog($app),
            $this->defaultDatabaseConfig
        );
    }

    /**
     * @param Application $application
     * @return Logger
     * @throws RuntimeAppContainerException
     */
    protected function getApplicationServiceLog(Application $application): Logger
    {
        $obj = $application->get(self::SERVICE_KEY_LOG);
        if (!$obj instanceof Logger) {
            throw RuntimeAppContainerException::appContainerReturnWrongType(Logger::class, get_class($obj));
        }
        return $obj;
    }

    /**
     * @param Application $application
     * @return DateService
     * @throws RuntimeAppContainerException
     */
    protected function getApplicationServiceDate(Application $application): DateService
    {
        $obj = $application->get(self::SERVICE_KEY_DATE);
        if (!$obj instanceof DateService) {
            throw RuntimeAppContainerException::appContainerReturnWrongType(DateService::class, get_class($obj));
        }
        return $obj;
    }

    /**
     * @param Application $application
     * @return Request
     * @throws RuntimeAppContainerException
     */
    protected function getApplicationServiceRequest(Application $application): Request
    {
        $obj = $application->get(self::SERVICE_KEY_REQUEST);
        if (!$obj instanceof Request) {
            throw RuntimeAppContainerException::appContainerReturnWrongType(Request::class, get_class($obj));
        }
        return $obj;
    }

    /**
     * @param Application $application
     * @return Response
     * @throws RuntimeAppContainerException
     */
    protected function getApplicationServiceResponse(Application $application): Response
    {
        $obj = $application->get(self::SERVICE_KEY_RESPONSE);
        if (!$obj instanceof Response) {
            throw RuntimeAppContainerException::appContainerReturnWrongType(Response::class, get_class($obj));
        }
        return $obj;
    }

    /**
     * @param Application $application
     * @return CodeLoader
     * @throws RuntimeAppContainerException
     */
    protected function getApplicationServiceBenchmark(Application $application): CodeLoader
    {
        $obj = $application->get(self::SERVICE_KEY_LOADER);
        if (!$obj instanceof CodeLoader) {
            throw RuntimeAppContainerException::appContainerReturnWrongType(CodeLoader::class, get_class($obj));
        }
        return $obj;
    }

    /**
     * @param Application $application
     * @return Language
     * @throws RuntimeAppContainerException
     */
    protected function getApplicationServiceLanguage(Application $application): Language
    {
        $obj = $application->get(self::SERVICE_KEY_LOADER);
        if (!$obj instanceof Language) {
            throw RuntimeAppContainerException::appContainerReturnWrongType(Language::class, get_class($obj));
        }
        return $obj;
    }
}


interface InitializedConfig
{
    public function initialize(Application $application);
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

    public function initialize(Application $application)
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

    public function initialize(Application $application)
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
    /** @var string  */
    public $templateNameAppend = '.tpl.php';
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
            'developing' => 'SYS_MODULE_PATH_Pattern_Modules_Developing',
            'developing/(:all)' => 'SYS_MODULE_PATH_Pattern_Modules_Developing/$1',
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

class DatabaseConfig
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

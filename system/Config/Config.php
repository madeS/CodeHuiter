<?php

namespace CodeHuiter\Config;

use CodeHuiter\Core\Application;

abstract class Config
{
    public const OPT_KEY_SINGLE = 'single';
    public const OPT_KEY_CLASS_APP = 'class_app';
    public const OPT_KEY_CLASS = 'class';
    public const OPT_KEY_CALLBACK = 'callback';

    public const SERVICE_KEY_REQUEST = 'request';
    public const SERVICE_KEY_RESPONSE = 'response';
    public const SERVICE_KEY_ROUTER = 'router';
    public const SERVICE_KEY_BENCHMARK = 'benchmark';
    public const SERVICE_KEY_LOG = 'log';
    public const SERVICE_KEY_CONSOLE = 'console';
    public const SERVICE_KEY_DEBUG = 'debug';
    public const SERVICE_KEY_EMAIL = 'email';
    public const SERVICE_KEY_DATE = 'date';
    public const SERVICE_KEY_LANG = 'lang';
    public const SERVICE_KEY_MIME_TYPES = 'mimeTypes';
    public const SERVICE_KEY_NETWORK = 'network';
    public const SERVICE_KEY_HTML_PARSER = 'htmlParser';
    public const SERVICE_KEY_DB = 'db';

    /**
     * @var array
     */
    public $services = [];

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
    /** @var EmailConfig */
    public $emailConfig;
    /** @var DateConfig */
    public $dateConfig;
    /** @var DatabaseConfig */
    public $defaultDatabaseConfig;

    public function __construct()
    {
        $this->settingsConfig = new SettingsConfig();
        $this->frameworkConfig = new FrameworkConfig();
        $this->requestConfig = new RequestConfig();
        $this->responseConfig = new ResponseConfig();
        $this->routerConfig = new RouterConfig();
        $this->logConfig = new LogConfig();
        $this->emailConfig = new EmailConfig();
        $this->dateConfig = new DateConfig();
        $this->defaultDatabaseConfig = new DatabaseConfig();

        $this->services[self::SERVICE_KEY_BENCHMARK] = [self::OPT_KEY_CLASS => '\\CodeHuiter\\Core\\Benchmark', self::OPT_KEY_SINGLE => true];
        $this->services[self::SERVICE_KEY_REQUEST] = [self::OPT_KEY_CLASS_APP => '\\CodeHuiter\\Core\\Request', self::OPT_KEY_SINGLE => true];
        $this->services[self::SERVICE_KEY_RESPONSE] = [self::OPT_KEY_CLASS_APP => '\\CodeHuiter\\Core\\Response', self::OPT_KEY_SINGLE => true];
        $this->services[self::SERVICE_KEY_ROUTER] = [self::OPT_KEY_CLASS_APP => '\\CodeHuiter\\Core\\Router', self::OPT_KEY_SINGLE => true];
        $this->services[self::SERVICE_KEY_LOG] = [self::OPT_KEY_CLASS_APP => '\\CodeHuiter\\Services\\Log\\Log', self::OPT_KEY_SINGLE => true];
        $this->services[self::SERVICE_KEY_CONSOLE] = [self::OPT_KEY_CLASS_APP => '\\CodeHuiter\\Services\\Console', self::OPT_KEY_SINGLE => true];
        $this->services[self::SERVICE_KEY_DEBUG] = [self::OPT_KEY_CLASS => '\\CodeHuiter\\Services\\Debug', self::OPT_KEY_SINGLE => true];
        $this->services[self::SERVICE_KEY_EMAIL] = [self::OPT_KEY_CLASS_APP => '\\CodeHuiter\\Services\\Email\\Mailer\\Mailer', self::OPT_KEY_SINGLE => true];
        $this->services[self::SERVICE_KEY_DATE] = [self::OPT_KEY_CLASS_APP => '\\CodeHuiter\\Services\\DateService', self::OPT_KEY_SINGLE => true];
        $this->services[self::SERVICE_KEY_LANG] = [self::OPT_KEY_CLASS => '\\CodeHuiter\\Services\\Language', self::OPT_KEY_SINGLE => true];
        $this->services[self::SERVICE_KEY_MIME_TYPES] = [self::OPT_KEY_CLASS => '\\CodeHuiter\\Config\\Data\\MimeTypes', self::OPT_KEY_SINGLE => true];
        $this->services[self::SERVICE_KEY_NETWORK] = [self::OPT_KEY_CLASS_APP => '\\CodeHuiter\\Services\\Network', self::OPT_KEY_SINGLE => true];
        $this->services[self::SERVICE_KEY_HTML_PARSER] = [self::OPT_KEY_CLASS => '\\CodeHuiter\\Services\\HtmlParser\\SimpleHtmlDomParser', self::OPT_KEY_SINGLE => true];

        $this->services[self::SERVICE_KEY_DB] = [self::OPT_KEY_SINGLE => true, self::OPT_KEY_CALLBACK => function(Application $app) {
            return new \CodeHuiter\Database\Drivers\PDODriver(
                $app->get(self::SERVICE_KEY_LOG), $app->config->defaultDatabaseConfig
            );
        }];
    }

    public function initialize(Application $application)
    {
        foreach ($this as $key => $value) {
            $config = $this->$key;
            if ($config instanceof InitializedConfig) {
                $config->initialize($application);
            }
        }
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
    public $error403 = ['controller' => '\\App\\Controllers\\Errors\\Error_Controller', 'controller_method' => 'error403'];
    public $error404 = ['controller' => '\\App\\Controllers\\Errors\\Error_Controller', 'controller_method' => 'error404'];
    public $error500 = ['controller' => '\\App\\Controllers\\Errors\\Error_Controller', 'controller_method' => 'error500'];
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
    public $directory = BASE_PATH . 'public_html/pub/logs/';
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

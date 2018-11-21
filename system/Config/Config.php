<?php

namespace CodeHuiter\Config;

use CodeHuiter\Core\Application;

abstract class Config
{
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

        $this->services[self::SERVICE_KEY_BENCHMARK] = ['single' => true, 'class' => '\\CodeHuiter\\Core\\Benchmark'];
        $this->services[self::SERVICE_KEY_REQUEST] = ['single' => true, 'class_app' => '\\CodeHuiter\\Core\\Request'];
        $this->services[self::SERVICE_KEY_RESPONSE] = ['single' => true, 'class_app' => '\\CodeHuiter\\Core\\Response'];
        $this->services[self::SERVICE_KEY_ROUTER] = ['single' => true, 'class_app' => '\\CodeHuiter\\Core\\Router'];
        $this->services[self::SERVICE_KEY_LOG] = ['single' => true, 'class_app' => '\\CodeHuiter\\Services\\Log\\Log'];
        $this->services[self::SERVICE_KEY_CONSOLE] = ['single' => true, 'class_app' => '\\CodeHuiter\\Services\\Console'];
        $this->services[self::SERVICE_KEY_DEBUG] = ['single' => true, 'class' => '\\CodeHuiter\\Services\\Debug'];
        $this->services[self::SERVICE_KEY_EMAIL] = ['single' => true, 'class_app' => '\\CodeHuiter\\Services\\Email\\Mailer\\Mailer'];
        $this->services[self::SERVICE_KEY_DATE] = ['single' => true, 'class_app' => '\\CodeHuiter\\Services\\DateService'];
        $this->services[self::SERVICE_KEY_LANG] = ['single' => true, 'class' => '\\CodeHuiter\\Services\\Language'];
        $this->services[self::SERVICE_KEY_MIME_TYPES] = ['single' => true, 'class' => '\\CodeHuiter\\Config\\Data\\MimeTypes'];
        $this->services[self::SERVICE_KEY_NETWORK] = ['single' => true, 'class_app' => '\\CodeHuiter\\Services\\Network'];
        $this->services[self::SERVICE_KEY_HTML_PARSER] = ['single' => true, 'class' => '\\CodeHuiter\\Services\\HtmlParser\\SimpleHtmlDomParser'];

        $this->services[self::SERVICE_KEY_DB] = ['single' => true, 'callback' => function(Application $app) {
            return new \CodeHuiter\Database\Drivers\PDODriver(
                $app->get(self::SERVICE_KEY_LOG), $app->config->defaultDatabaseConfig
            );
        }];
    }

    public function initialize()
    {
        foreach ($this as $key => $value) {
            $config = $this->$key;
            if ($config instanceof InitializedConfig) {
                $config->initialize();
            }
        }
    }
}


interface InitializedConfig
{
    public function initialize();
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

    public function initialize()
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

    public function initialize()
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

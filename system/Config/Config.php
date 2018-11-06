<?php

namespace CodeHuiter\Config;

abstract class Config
{
    public const CONFIG_KEY_MAIN = 'main';
    public const CONFIG_KEY_FRAMEWORK = 'framework';
    public const SERVICE_KEY_REQUEST = 'request';
    public const CONFIG_KEY_REQUEST = 'request';
    public const SERVICE_KEY_RESPONSE = 'response';
    public const CONFIG_KEY_RESPONSE = 'response';
    public const SERVICE_KEY_ROUTER = 'router';
    public const CONFIG_KEY_ROUTER = 'router';
    public const SERVICE_KEY_BENCHMARK = 'benchmark';
    public const SERVICE_KEY_LOG = 'log';
    public const CONFIG_KEY_LOG = 'log';
    public const SERVICE_KEY_CONSOLE = 'console';
    public const SERVICE_KEY_DEBUG = 'debug';
    public const SERVICE_KEY_EMAIL = 'email';
    public const CONFIG_KEY_EMAIL = 'email';
    public const SERVICE_KEY_DATE = 'date';
    public const CONFIG_KEY_DATE = 'date';
    public const SERVICE_KEY_LANG = 'lang';
    public const SERVICE_KEY_MIME_TYPES = 'mimeTypes';
    public const SERVICE_KEY_NETWORK = 'network';

    public const SERVICE_KEY_HTML_PARSER = 'htmlParser';

    public const SERVICE_KEY_DB = 'db';
    public const CONFIG_KEY_DB_DEFAULT = 'db_default';

    /**
     * @var array
     */
    public $configs = [];

    /**
     * @var array
     */
    public $services = [];

    public function __construct()
    {

        $this->configs[self::CONFIG_KEY_MAIN] = [
            'template' => 'default',
            'protocol' => 'http',
            'domain' => 'app.local',
            'language' => 'russian',
        ];

        $this->configs[self::CONFIG_KEY_FRAMEWORK] = [
            'show_debug_backtrace' => true,
            'show_errors' => true,
        ];

        $this->services[self::SERVICE_KEY_REQUEST] = ['single' => true, 'class_app' => '\\CodeHuiter\\Core\\Request'];
        $this->configs[self::CONFIG_KEY_REQUEST] = [
            // Allowed URL Characters
            'permitted_uri_chars' => 'a-z 0-9~%.:_\-',
        ];

        $this->services[self::SERVICE_KEY_RESPONSE] = ['single' => true, 'class_app' => '\\CodeHuiter\\Core\\Response'];
        $this->configs[self::CONFIG_KEY_RESPONSE] = [
            'charset' => 'UTF-8', // Recommended
            'template_name_append' => '.tpl.php',
            'profiler' => true,
            'profiler_placeholders' => [
                '{#result_time_table}',
                '{#result_class_table}',
                //'{#result_time}',
                //'{#result_memory}',
            ]
        ];

        $this->services[self::SERVICE_KEY_ROUTER] = ['single' => true, 'class_app' => '\\CodeHuiter\\Core\\Router'];
        $this->configs[self::CONFIG_KEY_ROUTER] = [
            'error_404' => ['controller' => '\\App\\Controllers\\Errors\\Error_Controller', 'controller_method' => 'error404'],
            'error_500' => ['controller' => '\\App\\Controllers\\Errors\\Error_Controller', 'controller_method' => 'error500'],
            'translate_uri' => [
                '-' => '_',
                '.' => '_dot_',
            ],
            'translate_uri_dashes' => false,
            'domain_routes' => [
                'all' => [
                    'developing' => 'SYS_MODULE_PATH_Pattern_Modules_Developing',
                    'developing/(:all)' => 'SYS_MODULE_PATH_Pattern_Modules_Developing/$1',
                ],
                'sub.example.com' => [
                    //'testmodule/(:all)' => 'APP_MODULE_TestModule/$1',
                ]
            ],
            'routes' => [
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
            ]
        ];

        $this->services[self::SERVICE_KEY_BENCHMARK] = ['single' => true, 'class' => '\\CodeHuiter\\Core\\Benchmark'];

        $this->services[self::SERVICE_KEY_LOG] = ['single' => true, 'class_app' => '\\CodeHuiter\\Services\\Log\\Log'];
        $this->configs[self::CONFIG_KEY_LOG] = [
            'threshold' => 'notice',
            'directory' => BASE_PATH . 'public_html/pub/logs/',
            'by_file' => '{#tag}_{#level}',
            'date_prepend' => 'Y-m',
            'file_permission' => 0777,
            'date_format' => 'Y-m-d H:i:s',
        ];

        $this->services[self::SERVICE_KEY_CONSOLE] = ['single' => true, 'class_app' => '\\CodeHuiter\\Services\\Console'];
        $this->services[self::SERVICE_KEY_DEBUG] = ['single' => true, 'class' => '\\CodeHuiter\\Services\\Debug'];

        $this->services[self::SERVICE_KEY_EMAIL] = ['single' => true, 'class_app' => '\\CodeHuiter\\Services\\Email\\Mailer\\Mailer'];
        $this->configs[self::CONFIG_KEY_EMAIL] = [
            'site_robot_email' => 'robot@app.local',
            'site_robot_name' => 'CodeHuiter Robot Name',
        ];

        $this->services[self::SERVICE_KEY_DATE] = ['single' => true, 'class_app' => '\\CodeHuiter\\Services\\DateService'];
        $this->configs[self::CONFIG_KEY_DATE] = [
            'site_timezone' => 'UTC',// TODO site timezone
        ];

        $this->services[self::SERVICE_KEY_LANG] = ['single' => true, 'class' => '\\CodeHuiter\\Services\\Language'];

        $this->services[self::SERVICE_KEY_MIME_TYPES] = ['single' => true, 'class' => '\\CodeHuiter\\Config\\Data\\MimeTypes'];

        $this->services[self::SERVICE_KEY_NETWORK] = ['single' => true, 'class_app' => '\\CodeHuiter\\Services\\Network'];
        $this->services[self::SERVICE_KEY_HTML_PARSER] = ['single' => true, 'class' => '\\CodeHuiter\\Services\\HtmlParser\\SimpleHtmlDomParser'];


        $this->services[self::SERVICE_KEY_DB] = ['single' => true, 'callback' => function(\CodeHuiter\Core\Application $app) {
            return new \CodeHuiter\Database\Drivers\PDOMySQL(
                $app->get(self::SERVICE_KEY_LOG), $app->getConfig(self::CONFIG_KEY_DB_DEFAULT)
            );
        }];

        $this->configs[self::CONFIG_KEY_DB_DEFAULT] = [
            'dsn' => 'mysql:host=localhost;dbname=app_db',
            'username' => 'appuser',
            'password' => 'apppassword',
            'debug' => true, // Save in memory data of time executing for totally print page
            'log_if_longer' => 10, // Logging queries if execute time longer than X ms
        ];

    }

    public function initialize() {
        $this->initErrorReporting();

        if (!isset($_SERVER['DOCUMENT_ROOT'])) {
            $_SERVER['DOCUMENT_ROOT'] = PUB_PATH;
        }

    }

    protected function initErrorReporting(){
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
    }
}

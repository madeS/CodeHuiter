<?php

namespace CodeHuiter\Config;

abstract class Config
{
    /** @var array $config */
    public $config;

    public function __construct()
    {
        $this->config = [
            'main' => [
                'template' => 'default'
            ],
            'framework' => [
                'show_debug_backtrace' => true,
                'show_errors' => true,
            ],
            'log' => [
                'threshold' => 'notice',
                'directory' => BASE_PATH . 'public_html/pub/logs/',
                'by_file' => '{#tag}_{#level}',
                'date_prepend' => 'Y-m',
                'file_permission' => 0777,
                'date_format' => 'Y-m-d H:i:s',
            ],
            'request' => [
                // Allowed URL Characters
                'permitted_uri_chars' => 'a-z 0-9~%.:_\-',
            ],
            'response' => [
                'charset' => 'UTF-8', // Recomended
                'template_name_append' => '.tpl.php',
                'profiler' => true,
                'profiler_placeholders' => [
                    '{#result_time_table}',
                    '{#result_class_table}',
                    //'{#result_time}',
                    //'{#result_memory}',
                ]
            ],
            'database_default' => [
                'dsn' => 'mysql:host=localhost;dbname=app_db',
                'username' => 'appuser',
                'password' => 'apppassword',
                'debug' => true, // Save in memory data of time executing for totally print page
                'log_if_longer' => 10, // Logging queries if execute time longer than X ms
            ],
            'router' => [
                'default' => ['controller' => '\\App\\Controllers\\Blog_Controller', 'controller_method' => 'index'],
                'error_404' => ['controller' => '\\App\\Controllers\\Errors\\Error_Controller', 'controller_method' => 'error404'],
                'error_500' => ['controller' => '\\App\\Controllers\\Errors\\Error_Controller', 'controller_method' => 'error500'],
                'translate_uri' => [
                    '-' => '_',
                    '.' => '_dot_',
                ],
                'translate_uri_dashes' => false,
                'domain_routes' => [
                    'sub.example.com' => [
                        //'testmodule/(:all)' => 'MODULE_TestModule/$1',
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
            ],
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

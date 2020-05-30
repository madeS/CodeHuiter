<?php

namespace CodeHuiter\Config\Core;

use CodeHuiter\Config\CoreConfig;
use CodeHuiter\Core\Application;
use CodeHuiter\Core\CodeLoader;
use CodeHuiter\Core\Router;
use CodeHuiter\Service\Logger;

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

    public static function populateConfig(CoreConfig $config): void
    {
        $config->routerConfig = new self();
        $config->services[Router::class] = ServiceConfig::forCallback(
            static function (Application $app) {
                return new Router(
                    $app,
                    $app->config->routerConfig,
                    $app->get(Logger::class),
                    $app->get(CodeLoader::class)
                );
            },
            ServiceConfig::SCOPE_REQUEST
        );
    }
}
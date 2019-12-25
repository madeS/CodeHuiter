<?php

namespace CodeHuiter\Core;

use CodeHuiter\Config\Config;
use CodeHuiter\Database\RelationalDatabase;
use CodeHuiter\Exception\CodeHuiterRuntimeException;
use CodeHuiter\Service;
use CodeHuiter\Service\Renderer;

/**
 * The base controller
 *
 * @property-read CodeLoader $loader
 * @see Config::SERVICE_KEY_LOADER There are Forward Usages
 *
 * @property-read Service\Logger $log
 * @see Config::SERVICE_KEY_LOG There are Forward Usages
 *
 * @property-read Service\Console $console
 * @see Config::SERVICE_KEY_CONSOLE There are Forward Usages
 *
 * @property-read Service\DateService $date
 * @see Config::SERVICE_KEY_DATE There are Forward Usages
 *
 * @property-read Service\Network $network
 * @see Config::SERVICE_KEY_NETWORK There are Forward Usages
 *
 * @property-read Service\Language $lang
 * @see Config::SERVICE_KEY_LANG There are Forward Usages
 *
 * @property-read Service\HtmlParser $htmlParser
 * @see Config::SERVICE_KEY_HTML_PARSER There are Forward Usages
 *
 * @property-read Service\Mailer $email
 * @see Config::SERVICE_KEY_EMAIL There are Forward Usages
 *
 * @property-read Renderer $renderer
 * @see Config::SERVICE_KEY_RENDERER There are Forward Usages
 *
 * @property-read Request $request
 * @see Config::SERVICE_KEY_REQUEST There are Forward Usages
 *
 * @property-read Response $response
 * @see Config::SERVICE_KEY_RESPONSE There are Forward Usages
 *
 * @property-read Router $router
 * @see Config::SERVICE_KEY_ROUTER There are Forward Usages
 *
 * @property-read RelationalDatabase $db
 * @see Config::SERVICE_KEY_DB_DEFAULT There are Forward Usages
 */
class Controller
{
    /** @prop */

    /** @var Controller $instance */
    private static $instance;

    /** @var Application $app */
    public $app;

    public function __construct(Application $app)
    {
        self::$instance = $this;
        $this->app = $app;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        if (isset($this->app->config->injectedServices[$name])) {
            $this->$name = $this->app->get($this->app->config->injectedServices[$name]);
            return $this->$name;
        }
        throw new CodeHuiterRuntimeException("injected field $name not found");
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    /**
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->app->config->injectedServices[$name]);
    }

    /**
     * @return Controller
     */
    public static function getInstance(): Controller
    {
        return self::$instance;
    }
}

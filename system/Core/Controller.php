<?php

namespace CodeHuiter\Core;

use CodeHuiter\Config\CoreConfig;
use CodeHuiter\Exception\CodeHuiterRuntimeException;
use CodeHuiter\Service;
use CodeHuiter\Service\Renderer;

/**
 * The base controller
 *
 * @property-read CodeLoader $loader
 * @see CoreConfig::INJECTED_LOADER There are Forward Usages
 *
 * @property-read Service\Logger $log
 * @see CoreConfig::INJECTED_LOG There are Forward Usages
 *
 * @property-read Service\Console $console
 * @see CoreConfig::INJECTED_CONSOLE There are Forward Usages
 *
 * @property-read Service\DateService $date
 * @see CoreConfig::INJECTED_DATE There are Forward Usages
 *
 * @property-read Service\Network $network
 * @see CoreConfig::INJECTED_NETWORK There are Forward Usages
 *
 * @property-read Service\Language $lang
 * @see CoreConfig::INJECTED_LANG There are Forward Usages
 *
 * @property-read Renderer $renderer
 * @see CoreConfig::INJECTED_RENDERER There are Forward Usages
 *
 * @property-read Request $request
 * @see CoreConfig::INJECTED_REQUEST There are Forward Usages
 *
 * @property-read Response $response
 * @see CoreConfig::INJECTED_RESPONSE There are Forward Usages
 *
 * @property-read Router $router
 * @see CoreConfig::INJECTED_ROUTER There are Forward Usages
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

<?php

namespace CodeHuiter\Core;

use CodeHuiter\Database\AbstractDatabase;

/**
 * The base controller
 *
 * @property-read CodeLoader $loader
 * @property-read Router $router
 * @property-read Request $request
 * @property-read Response $response
 * @property-read AbstractDatabase $db
 * @property-read \CodeHuiter\Service\Logger $log
 * @property-read \CodeHuiter\Service\Console $console
 * @property-read \CodeHuiter\Service\Debug $debug
 * @property-read \CodeHuiter\Service\Language $lang
 * @property-read \CodeHuiter\Service\DateService $date
 * @property-read \CodeHuiter\Service\Network $network
 * @property-read \CodeHuiter\Service\HtmlParser $htmlParser
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
        $this->$name = $this->app->get($name);

        return $this->$name;
    }

    /**
     * @return Controller
     */
    public static function getInstance()
    {
        return self::$instance;
    }
}

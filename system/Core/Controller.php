<?php

namespace CodeHuiter\Core;

use CodeHuiter\Database\AbstractDatabase;

/**
 * The base controller
 *
 * @property-read Benchmark $benchmark
 * @property-read Router $router
 * @property-read Request $request
 * @property-read Response $response
 * @property-read AbstractDatabase $db
 * @property-read \CodeHuiter\Core\Log\AbstractLog $log
 * @property-read \CodeHuiter\Services\Console $console
 * @property-read \CodeHuiter\Services\Debug $debug
 * @property-read \CodeHuiter\Services\Language $lang
 * @property-read \CodeHuiter\Services\DateService $date
 * @property-read \CodeHuiter\Services\Network $network
 * @property-read \CodeHuiter\Services\HtmlParser\HtmlParserInterface $htmlParser
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

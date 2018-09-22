<?php

namespace CodeHuiter\Core;

use CodeHuiter\Config\Config;
use CodeHuiter\Core\Exceptions\ExceptionProcessor;
use CodeHuiter\Exceptions\AppContainerException;
use CodeHuiter\Exceptions\CodeHuiterException;

class Application
{
    /**
     * @var Application $instance
     */
    protected static $instance = null;

    /**
     * @return Application
     * @throws AppContainerException
     */
    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /** @var string $environment */
    protected $environment;

    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * @param string $key
     * @return array
     */
    public function getConfig($key)
    {
        return $this->configs->config[$key] ?? [];
    }

    /**
     * @return Config
     */
    public function getConfigs()
    {
        return $this->configs;
    }

    /** @var Config */
    protected $configs;

    /**
     * Application constructor.
     * @throws AppContainerException
     */
    protected function __construct() {
        // Load Services
        $this->services = array_merge(
            $this->requireVarIfFileExist(SYSTEM_PATH . 'Config/Services.php', []),
            $this->requireVarIfFileExist(APP_PATH . 'Config/Services.php', [])
        );
        
        //$config = $this->get('config');
        
        // Load main Configs
        $this->environment = $this->requireVarIfFileExist(APP_PATH . 'Config/Env.php', 'Developing');
        $this->configs = $this->get('config');
        $this->configs->initialize();
    }

    /**
     * @param string $fileName
     * @param null $default
     * @return mixed|null
     * @throws AppContainerException
     */
    protected function requireVarIfFileExist($fileName, $default = null) {
        if (file_exists($fileName) && is_file($fileName)) {
            $result = require $fileName;
            if (gettype($result) !== gettype($default)) {
                throw new AppContainerException('Invalid type returned from ['.$fileName.'] Returned: ['.gettype($result).'], Expected: ['.gettype($default).']');
            }
            return $result;
        } else {
            return $default;
        }
    }
    
    /**
     * ---------------------------
     * Dependency Injection
     */

    /**
     * @var array
     */
    protected $container = [];

    /**
     * @var array
     */
    protected $services;

    /**
     * @param string $name Key of object
     * @param array $params To
     * @return mixed
     */
    public function get($name){
        if(!isset($this->services[$name])) {
            ExceptionProcessor::defaultProcessException(
                new AppContainerException("Class [$name] not found in services")
            );
        }
        if (
            isset($this->container[$name])
            && isset($this->services[$name]['single']) && $this->services[$name]['single']
        ){
            return $this->container[$name];
        }
        if (isset($this->services[$name]['callback']) && $this->services[$name]['callback']) {
            $callback = $this->services[$name]['callback'];
            $this->container[$name] = $callback($this);
        } elseif (isset($this->services[$name]['class']) && $this->services[$name]['class']) {
            $class = $this->services[$name]['class'];
            $this->container[$name] = new $class();
        } elseif (isset($this->services[$name]['class_app']) && $this->services[$name]['class_app']) {
            $class = $this->services[$name]['class_app'];
            $this->container[$name] = new $class($this);
        } else {
            ExceptionProcessor::defaultProcessException(
                new AppContainerException("Class [$name] not provide object creation information")
            );
        }

        return $this->container[$name];
    }
    
    public function set($name, $instance){
        $this->container[$name] = $instance;
    }

    /**
     * Run the application
     */
    public function run() {
        try {
            /** @var Router $router */
            $router = $this->get('router');
            $router->execute();

            /** @var Response $response */
            $response = $this->get('response');
            $response->send();

        } catch (CodeHuiterException $ex) {
            \CodeHuiter\Core\Exceptions\ExceptionProcessor::defaultProcessException($ex);
        }
    }

}



<?php

namespace CodeHuiter\Core;

use App\Config\DefaultConfig;
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
    public static function getInstance(): Application
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @var string
     */
    protected $environment;

    /**
     * @var DefaultConfig
     */
    public $config;

    /**
     * @var array
     */
    protected $container = [];

    public function getEnvironment(): ?string
    {
        return $this->environment;
    }

    /**
     * Application constructor.
     * @throws AppContainerException
     */
    protected function __construct()
    {
        $this->environment = $this->requireVarIfFileExist(APP_PATH . 'Config/Env.php', 'Developing');

        $configClassName = "\\App\\Config\\{$this->environment}Config";
        $this->config = new $configClassName();
        $this->config->initialize();
    }

    /**
     * @param string $fileName
     * @param null $default
     * @return mixed|null
     * @throws AppContainerException
     */
    protected function requireVarIfFileExist($fileName, $default = null)
    {
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
     * @param string $name Key of object
     * @return mixed
     */
    public function get($name)
    {
        if(!isset($this->config->services[$name])) {
            ExceptionProcessor::defaultProcessException(
                new AppContainerException("Class [$name] not found in services")
            );
        }
        if (
            isset($this->container[$name])
            && isset($this->config->services[$name]['single']) && $this->config->services[$name]['single']
        ){
            return $this->container[$name];
        }
        if (isset($this->config->services[$name]['callback']) && $this->config->services[$name]['callback']) {
            $callback = $this->config->services[$name]['callback'];
            $this->container[$name] = $callback($this);
        } elseif (isset($this->config->services[$name]['class']) && $this->config->services[$name]['class']) {
            $class = $this->config->services[$name]['class'];
            $this->container[$name] = new $class();
        } elseif (isset($this->config->services[$name]['class_app']) && $this->config->services[$name]['class_app']) {
            $class = $this->config->services[$name]['class_app'];
            $this->container[$name] = new $class($this);
        } else {
            ExceptionProcessor::defaultProcessException(
                new AppContainerException("Class [$name] not provide object creation information")
            );
        }

        return $this->container[$name];
    }
    
    public function set($name, $instance): void
    {
        $this->container[$name] = $instance;
    }

    /**
     * Run the application
     */
    public function run(): void
    {
        try {
            /** @var Router $router */
            $router = $this->get(Config::SERVICE_KEY_ROUTER);
            $router->execute();

            /** @var Response $response */
            $response = $this->get(Config::SERVICE_KEY_RESPONSE);
            $response->send();

        } catch (CodeHuiterException $ex) {
            \CodeHuiter\Core\Exceptions\ExceptionProcessor::defaultProcessException($ex);
        }
    }
}

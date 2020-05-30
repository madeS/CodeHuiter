<?php

namespace CodeHuiter\Core;

use App\Config\DefaultConfig;
use CodeHuiter\Config\Core\ServiceConfig;
use CodeHuiter\Core\Exception\ExceptionProcessor;
use CodeHuiter\Exception\Runtime\CoreException;
use Exception;

class Application
{
    /**
     * @var Application $instance
     */
    protected static $instance;

    /**
     * @return Application
     */
    public static function getInstance(): Application
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function started(): bool
    {
        return (bool)self::$instance && self::$instance->request;
    }

    public function destroy(): void
    {
        self::$instance = null;
    }

    /**
     * @var DefaultConfig
     */
    public $config;

    /**
     * @var array
     */
    protected $container = [];

    protected $serviceCreateStack = [];

    /**
     * @var Request
     */
    private $request;

    protected function __construct()
    {
        $this->init();
    }

    /**
     * @param string|null $environmentConfigClass
     */
    public function init(?string $environmentConfigClass = null): void
    {
        if ($environmentConfigClass === null) {
            $environmentConfigClass = $this->requireVarIfFileExist(APP_PATH . 'Config/Env.php', 'Developing');
        }

        $this->config = new $environmentConfigClass();
        $this->config->initialize($this);
    }

    /**
     * Get service by key
     * @param string $name Key of object
     * @return mixed
     */
    public function get(string $name)
    {
        $result = null;
        if(!isset($this->config->services[$name])) {
            throw CoreException::onServiceNotFound($name);
        }
        $serviceConfig = $this->config->services[$name];
        if (!$serviceConfig instanceof ServiceConfig) {
            throw CoreException::onServiceConfigInvalid($name);
        }

        $scope = $serviceConfig->scope ?? ServiceConfig::SCOPE_PERMANENT;
        if ($scope === ServiceConfig::SCOPE_REQUEST) {
            $scope .= $this->request ? $this->request->getId() : 0;
        }
        if (isset($this->serviceCreateStack[$scope][$name])) {
            throw CoreException::onRecursiveServiceCreation($name, $scope, $this->serviceCreateStack);
        }
        $this->serviceCreateStack[$scope][$name] = true;

        if ($scope === ServiceConfig::SCOPE_NO_SHARED || !isset($this->container[$scope][$name])) {
            $obj = null;
            if ($serviceConfig->type === ServiceConfig::TYPE_CALLBACK && $serviceConfig->callback) {
                $callback = $serviceConfig->callback;
                $obj = $callback($this);
            } elseif ($serviceConfig->type === ServiceConfig::TYPE_CLASS) {
                $class = $serviceConfig->className;
                $obj = new $class();
            } elseif ($serviceConfig->type === ServiceConfig::TYPE_CLASS_APP) {
                $class = $serviceConfig->className;
                $obj = new $class($this);
            } else {
                throw CoreException::onServiceNotProvideCreationInfo($name);
            }
            $validateClass = $serviceConfig->validateClassName ?? $name;
            if ($validateClass !== '') {
                if (!is_a($obj, $validateClass)) {
                    throw CoreException::onServiceValidationNotPassed($name, $validateClass, get_class($obj));
                }
            }
            $this->container[$scope][$name] = $obj;
        }

        unset($this->serviceCreateStack[$scope][$name]);
        return $this->container[$scope][$name];
    }

    /**
     * @param string $name
     * @return bool
     */
    public function serviceExist(string $name): bool
    {
        return isset($this->config->services[$name]);
    }

    /**
     * Set service
     * @param string $name
     * @param mixed $instance
     */
    public function set(string $name, $instance): void
    {
        if(!isset($this->config->services[$name])) {
            throw CoreException::onServiceNotFound($name);
        }
        $serviceConfig = $this->config->services[$name];

        $scope = $serviceConfig->scope ?? ServiceConfig::SCOPE_PERMANENT;
        if ($scope === ServiceConfig::SCOPE_REQUEST) {
            $scope .= $this->request->getId();
        }

        $this->container[$scope][$name] = $instance;
        if ($instance === null) {
            unset($this->container[$scope][$name]);
        }
    }


    /**
     * Run the application
     * @param Request|null $request
     * @return Response
     */
    public function run(?Request $request): ?Response
    {
        try {
            if ($request === null) {
                $request = $this->get(Request::class);
            } else {
                $this->set(Request::class, $request);
            }
            $this->request = $request;

            /** @var Router $router */
            $router = $this->get(Router::class);
            $router->init($this->request)->execute();

            /** @var Response $response */
            $response = $this->get(Response::class);

            $this->destroyScope(ServiceConfig::SCOPE_REQUEST . $this->request->getId());

            $this->request = null;
            return $response;
        } catch (Exception $ex) {
            ExceptionProcessor::defaultProcessException($ex);
        }
        return null;
    }

    /**
     * @param string $fileName
     * @param null $default
     * @return mixed|null
     */
    protected function requireVarIfFileExist($fileName, $default = null)
    {
        if (file_exists($fileName) && is_file($fileName)) {
            $result = require $fileName;
            if (gettype($result) !== gettype($default)) {
                throw CoreException::onInvalidRequireVarType($fileName, gettype($result), gettype($default));
            }
            return $result;
        }
        return $default;
    }

    /**
     * Destroy container scope
     * @param string $scope
     */
    private function destroyScope(string $scope): void
    {
        unset($this->container[$scope]);
    }
}

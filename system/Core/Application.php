<?php

namespace CodeHuiter\Core;

use App\Config\DefaultConfig;
use CodeHuiter\Config\Config;
use CodeHuiter\Core\Event\ApplicationEvent;
use CodeHuiter\Core\ByDefault\ApplicationEventSubscription;
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

    /**
     * @var array
     */
    protected $subscriptions = [];

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

        $scope = $this->config->services[$name][Config::OPT_KEY_SCOPE] ?? Config::OPT_KEY_SCOPE_PERMANENT;
        if ($scope === Config::OPT_KEY_SCOPE_REQUEST) {
            $scope .= $this->request->getId();
        }
        if (isset($this->serviceCreateStack[$scope][$name])) {
            throw CoreException::onRecursiveServiceCreation($name, $scope, $this->serviceCreateStack);
        }
        $this->serviceCreateStack[$scope][$name] = true;

        if ($scope === Config::OPT_KEY_SCOPE_NEW || !isset($this->container[$scope][$name])) {
            $obj = null;
            if (isset($this->config->services[$name][Config::OPT_KEY_CALLBACK]) && $this->config->services[$name][Config::OPT_KEY_CALLBACK]) {
                $callback = $this->config->services[$name][Config::OPT_KEY_CALLBACK];
                $obj = $callback($this);
            } elseif (isset($this->config->services[$name][Config::OPT_KEY_CLASS]) && $this->config->services[$name][Config::OPT_KEY_CLASS]) {
                $class = $this->config->services[$name][Config::OPT_KEY_CLASS];
                $obj = new $class();
            } elseif (isset($this->config->services[$name][Config::OPT_KEY_CLASS_APP]) && $this->config->services[$name][Config::OPT_KEY_CLASS_APP]) {
                $class = $this->config->services[$name][Config::OPT_KEY_CLASS_APP];
                $obj = new $class($this);
            } else {
                throw CoreException::onServiceNotProvideCreationInfo($name);
            }
            $validateClass = $this->config->services[$name][Config::OPT_KEY_VALIDATE] ?? $name;
            if ($validateClass !== false) {
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

        $scope = $this->config->services[$name][Config::OPT_KEY_SCOPE] ?? Config::OPT_KEY_SCOPE_PERMANENT;
        if ($scope === Config::OPT_KEY_SCOPE_REQUEST) {
            $scope .= $this->request->getId();
        }

        $this->container[$scope][$name] = $instance;
        if ($instance === null) {
            unset($this->container[$scope][$name]);
        }
    }

    /**
     * @param ApplicationEvent $event
     */
    public function fireEvent(ApplicationEvent $event): void
    {
        $eventClass = get_class($event);
        if (!isset($this->subscriptions[$eventClass])) {
            return;
        }
        /** @var ApplicationEventSubscription $subscription */
        foreach ($this->subscriptions[$eventClass] as $subscription) {
            $subscriber = $subscription->getSubscriber($this, $eventClass);
            $subscriber->catchEvent($event);
        }
    }

    /**
     * @param $handler
     * @param string $eventClass
     * @param int $priority
     */
    public function subscribe($handler, string $eventClass, int $priority = 1) : void
    {
        if (!isset($this->subscriptions[$eventClass])) {
            $this->subscriptions[$eventClass] = [];
        }
        $this->subscriptions[$eventClass][] = new ApplicationEventSubscription($handler, $priority);
        usort($this->subscriptions[$eventClass], static function (ApplicationEventSubscription $a, ApplicationEventSubscription $b) {
            return !($a->priority <=> $b->priority);
        });
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

            $this->destroyScope(Config::OPT_KEY_SCOPE_REQUEST . $this->request->getId());

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

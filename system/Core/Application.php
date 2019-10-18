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

    /**
     * @var array
     */
    protected $subscriptions = [];

    protected $serviceCreateStack = [];

    /**
     * Application constructor.
     */
    protected function __construct()
    {
        $this->environment = $this->requireVarIfFileExist(APP_PATH . 'Config/Env.php', 'Developing');

        $configClassName = "\\App\\Config\\{$this->environment}Config";
        $this->config = new $configClassName();
        $this->config->initialize($this);
    }

    public function getEnvironment(): string
    {
        return $this->environment;
    }

    /**
     * Get service by key
     * @param string $name Key of object
     * @return mixed
     */
    public function get(string $name)
    {
        if (isset($this->serviceCreateStack[$name])) {
            throw CoreException::onRecursiveServiceCreation($name, $this->serviceCreateStack);
        }
        $this->serviceCreateStack[$name] = true;

        $result = null;
        if(!isset($this->config->services[$name])) {
            throw CoreException::onServiceNotFound($name);
        }
        if (
            !isset($this->container[$name])
            || !isset($this->config->services[$name][Config::OPT_KEY_SINGLE])
            || !$this->config->services[$name][Config::OPT_KEY_SINGLE]
        ) {
            if (isset($this->config->services[$name][Config::OPT_KEY_CALLBACK]) && $this->config->services[$name][Config::OPT_KEY_CALLBACK]) {
                $callback = $this->config->services[$name][Config::OPT_KEY_CALLBACK];
                $this->container[$name] = $callback($this);
            } elseif (isset($this->config->services[$name][Config::OPT_KEY_CLASS]) && $this->config->services[$name][Config::OPT_KEY_CLASS]) {
                $class = $this->config->services[$name][Config::OPT_KEY_CLASS];
                $this->container[$name] = new $class();
            } elseif (isset($this->config->services[$name][Config::OPT_KEY_CLASS_APP]) && $this->config->services[$name][Config::OPT_KEY_CLASS_APP]) {
                $class = $this->config->services[$name][Config::OPT_KEY_CLASS_APP];
                $this->container[$name] = new $class($this);
            } else {
                throw CoreException::onServiceNotProvideCreationInfo($name);
            }
            if (isset($this->config->services[$name][Config::OPT_KEY_VALIDATE])) {
                if (!is_subclass_of($this->container[$name], $this->config->services[$name][Config::OPT_KEY_VALIDATE])) {
                    throw CoreException::onServiceValidationNotPassed(
                        $name, $this->config->services[$name][Config::OPT_KEY_VALIDATE], get_class($this->container[$name])
                    );
                }
            }
        }

        unset($this->serviceCreateStack[$name]);
        return $this->container[$name];
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
        $this->container[$name] = $instance;
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
     */
    public function run(): void
    {
        try {
            /** @var Router $router */
            $router = $this->get(Config::SERVICE_KEY_ROUTER);
            $router->execute();

            /** @var Response $response */
            $response = $this->get(Response::class);
            $response->send();
        } catch (Exception $ex) {
            ExceptionProcessor::defaultProcessException($ex);
        }
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
}

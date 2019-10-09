<?php

namespace CodeHuiter\Core;

use App\Config\DefaultConfig;
use CodeHuiter\Config\Config;
use CodeHuiter\Core\Event\ApplicationEvent;
use CodeHuiter\Core\Event\ApplicationEventSubscription;
use CodeHuiter\Core\Exception\ExceptionProcessor;
use CodeHuiter\Core\Exception\ExceptionThrower;
use CodeHuiter\Core\Exception\ExceptionThrowerInterface;
use CodeHuiter\Exception\AppContainerException;
use CodeHuiter\Exception\CodeHuiterException;
use CodeHuiter\Service\Renderer;
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
            $this->fireException(new AppContainerException("Recursive Service [$name] creation found: " . print_r($this->serviceCreateStack, true)));
        }
        $this->serviceCreateStack[$name] = true;

        $result = null;
        if(!isset($this->config->services[$name])) {
            $this->fireException(new AppContainerException("Class [$name] not found in services"));
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
                $this->fireException(
                    new AppContainerException("Class [$name] not provide object creation information")
                );
            }
            if (isset($this->config->services[$name][Config::OPT_KEY_VALIDATE])) {
                if (!is_subclass_of($this->container[$name], $this->config->services[$name][Config::OPT_KEY_VALIDATE])) {
                    $this->fireException(
                        new AppContainerException(
                            "Class [$name] provide object with validation fail. "
                            . "Expect: {$this->config->services[$name][Config::OPT_KEY_VALIDATE]}, got " .get_class()
                        )
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
            $subscriber = $subscription->getSubscriber($this);
            if ($subscriber) {
                $subscriber->catchEvent($event);
            }
        }
    }

    /**
     * @param $handler
     * @param string $event
     * @param int $priority
     */
    public function subscribe($handler, string $event, int $priority = 1) : void
    {
        if (!isset($this->subscriptions[$event])) {
            $this->subscriptions[$event] = [];
        }
        $this->subscriptions[$event][] = new ApplicationEventSubscription($handler, $priority);
        usort($this->subscriptions[$event], static function (ApplicationEventSubscription $a, ApplicationEventSubscription $b) {
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

        } catch (CodeHuiterException $ex) {
            ExceptionProcessor::defaultProcessException($ex);
        }
    }

    /**
     * @param Exception $exception
     */
    public function fireException(Exception $exception): void
    {
        $this->getThrower()->fire($exception);
    }

    /**
     * @return ExceptionThrowerInterface
     */
    protected function getThrower(): ExceptionThrowerInterface
    {
        return new ExceptionThrower();
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
                $this->fireException(new AppContainerException(
                    'Invalid type returned from ['.$fileName.'] Returned: ['.gettype($result).'], Expected: ['.gettype($default).']'
                ));
            }
            return $result;
        }
        return $default;
    }
}

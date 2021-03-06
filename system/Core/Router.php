<?php

namespace CodeHuiter\Core;

use CodeHuiter\Config\RouterConfig;
use CodeHuiter\Service\Logger;
use CodeHuiter\Exception\Runtime\CoreException;
use CodeHuiter\Exception\InvalidConfigException;
use CodeHuiter\Exception\InvalidRequestException;

class Router
{
    /** @var Request $request */
    protected $request;

    /** @var RouterConfig  */
    protected $config;

    /** @var string */
    protected $directory = APP_PATH . 'Controller/';

    /** @var string */
    protected $namespace = '\\App\\Controller\\';

    /** @var string $controller */
    protected $controller;

    /** @var string $controllerMethod */
    protected $controllerMethod;

    /** @var array $controllerMethodParams */
    protected $controllerMethodParams;

    /** @var Application $app */
    protected $app;

    /** @var Controller $controllerInstance */
    protected $controllerInstance;

    /** @var CodeLoader $loader  */
    protected $loader;

    /** @var Logger */
    protected $log;

    /**
     * @param Application $app
     * @param RouterConfig $config
     * @param Logger $log
     * @param CodeLoader $loader
     */
    public function __construct(
        Application $app,
        RouterConfig $config,
        Logger $log,
        CodeLoader $loader
    ) {
        $this->config = $config;
        $this->app = $app;
        $this->loader = $loader;
        $this->log = $log;
    }

    public function init(Request $request): Router
    {
        $this->request = $request;

        $segments = $this->checkRoutingRewriteSegments();
        if ($segments === null) {
            $segments = $this->request->getSegments();
        }
        $this->processSegments($segments);
        return $this;
    }

    public function execute(): void
    {
        try {
            if (!class_exists($this->controller,true)) {
                throw new InvalidRequestException("Class '{$this->controller}' not found for this request'");
            }
            if (method_exists('\\CodeHuiter\\Core\\Controller', $this->controllerMethod)) {
                throw new InvalidRequestException(
                    "Method '{$this->controllerMethod}' is system method and not allowed"
                );
            }
            if (!method_exists($this->controller, $this->controllerMethod)) {
                throw new InvalidRequestException(
                    "Method '{$this->controllerMethod}' not found'
                    . ' in class '{$this->controller}' for this request'"
                );
            }

            try {
                $reflection = new \ReflectionMethod($this->controller, $this->controllerMethod);
                if (!$reflection->isPublic() OR $reflection->isConstructor())
                {
                    throw new InvalidRequestException(
                        "Method '{$this->controllerMethod}' not public'
                        . ' in class '{$this->controller}' for this request'"
                    );
                }

                // Controller run
                $this->loader->benchmarkPoint('CreateController');
                $this->controllerInstance = new $this->controller($this->app);
                $this->loader->benchmarkPoint('RunController');
                $this->controllerInstance->{$this->controllerMethod}(...$this->controllerMethodParams);

            } catch (\ReflectionException $exception) {
                throw new InvalidRequestException(
                    "Catching ReflectionMethodException for class '{$this->controller}' and method {$this->controllerMethod} "
                );
            }
        } catch (InvalidRequestException $exception) {

            $this->log->notice('Page not found: ' . $this->request->getUri());

            if ($this->controller === $this->config->error404['controller']) {
                throw CoreException::onErrorControllerNoFound($this->controller, $this->controllerMethod, $exception);
            }
            $this->setController($this->config->error404['controller'], true);
            $this->setControllerMethod($this->config->error404['controller_method']);
            $this->setControllerMethodParams([$exception]);
            $this->execute();
        }
    }

    /**
     * @param string $routeKey
     * @param array $params
     * @throws InvalidConfigException
     */
    public function setRouting($routeKey, $params)
    {
        if (
            !isset($this->config->$routeKey)
            || !isset($this->config->$routeKey['controller'])
            || !isset($this->config->$routeKey['controller_method'])
        ) {
            throw new InvalidConfigException("Not correct exist config.router.{$routeKey} ");
        }
        $this->setController($this->config->$routeKey['controller'], true);
        $this->setControllerMethod($this->config->$routeKey['controller_method']);
        $this->setControllerMethodParams($params);
    }

    /**
     * @param string $dir
     * @param string $root
     */
    protected function setDirectory(string $dir, $root = '')
    {
        if ($root || empty($this->directory)) {
            $this->directory = $root . str_replace('.', '', $dir) . '/';
        } else {
            $this->directory .= str_replace('.', '', $dir) . '/';
        }
    }

    /**
     * @param string $space
     * @param string $root
     */
    protected function setNamespace(string $space, $root = '')
    {
        if ($root || empty($this->namespace)) {
            $this->namespace = $root . str_replace('/', '\\', $space) . '\\';
        } else {
            $this->namespace .= str_replace('/', '\\', $space) . '\\';
        }
    }

    /**
     * @param string|null $controllerName
     * @param bool $isFullName
     */
    protected function setController($controllerName = null, $isFullName = false)
    {
        if ($isFullName) {
            $this->controller = $controllerName;
        } else {
            $this->controller = $this->namespace . ($controllerName ?? 'Main') . '_Controller';
        }
    }

    /**
     * @param string|null $controllerMethodName
     */
    protected function setControllerMethod($controllerMethodName = null)
    {
        $this->controllerMethod = ($controllerMethodName)
            ? $controllerMethodName : 'index';
    }

    /**
     * @param array|null $controllerMethodParams
     */
    protected function setControllerMethodParams($controllerMethodParams = null)
    {
        $this->controllerMethodParams = ($controllerMethodParams)
            ? $controllerMethodParams : [];

        foreach($this->controllerMethodParams as $key => $value) {
            $this->controllerMethodParams[$key] = urldecode($value);
        }
    }

    /**
     * Check request uri segments for match routes config.
     * If yes return new segments
     * @return array|null
     */
    protected function checkRoutingRewriteSegments()
    {
        $uri = implode('/', $this->request->getSegments());
        $http_verb = $this->request->getMethod();

        $routes = $this->config->routes;
        if (isset($this->config->domainRoutes['all'])) {
            $routes = array_merge($routes, $this->config->domainRoutes['all']);
        }
        if (isset($this->config->domainRoutes[$this->request->getDomain()])) {
            $routes = array_merge($routes, $this->config->domainRoutes[$this->request->getDomain()]);
        }

        // Loop through the route array looking for wildcards
        foreach ($routes as $key => $val)
        {
            if (is_array($val)) {
                $val = array_change_key_case($val, CASE_LOWER);
                if (isset($val[$http_verb])) {
                    $val = $val[$http_verb];
                } else {
                    continue;
                }
            }

            // Convert wildcards to RegEx
            $key = str_replace([':any', ':num', ':all'], ['[^/]+', '[0-9]+', '.+'], $key);

            // Does the RegEx match?
            if (preg_match('#^'.$key.'$#', $uri, $matches))
            {
                // Are we using callbacks to process back-references?
                if (!is_string($val) && is_callable($val))
                {
                    // Remove the original string from the matches array.
                    array_shift($matches);

                    // Execute the callback using the values in matches as its parameters.
                    $val = call_user_func_array($val, $matches);
                }
                // Are we using the default routing method for back-references?
                elseif (strpos($val, '$') !== false && strpos($key, '(') !== false)
                {
                    $val = preg_replace('#^'.$key.'$#', $val, $uri);
                }

                return explode('/', $val);

            }
        }

        return null;
    }

    /**
     * Cut segments and set directory
     * @param array $segments
     * @return array
     */
    protected function shiftDirectoriesFromSegments(array $segments)
    {
        $segmentsCount = count($segments);

        for ($i = 0; $i < $segmentsCount; $i++) {
            $segmentTest = $this->translateUriPart($segments[0]);
            if ($i === 0) {
                if (strpos($segmentTest, 'APP_MODULE_') === 0) {
                    $moduleName = substr($segmentTest, strlen('APP_MODULE_'));
                    $this->setDirectory( 'Module/'.$moduleName.'/Controller', APP_PATH);
                    $this->setNamespace('', '\\App\\Module\\' . $moduleName . '\\Controller');
                    array_shift($segments);
                    continue;
                }
                if (strpos($segmentTest, 'SYS_MODULE_PATH_') === 0) {
                    $modulePath = str_replace('_','/',substr($segmentTest, strlen('SYS_MODULE_PATH_')));
                    $this->setDirectory( $modulePath . '/Controller', SYSTEM_PATH);
                    $this->setNamespace($modulePath . '\\Controller', '\\CodeHuiter\\');
                    array_shift($segments);
                    continue;
                }
                $this->setDirectory( 'Controller', APP_PATH);
                $this->setNamespace('', '\\App\\Controller');
            }
            $nameTest = $this->directory . $segmentTest;

            if (is_dir($nameTest) && !file_exists($nameTest . '_Controller.php')) {
                array_shift($segments);
                $this->setDirectory($segmentTest, '');
                $this->setNamespace($segmentTest, '');
                continue;
            }

            return $segments;
        }

        // This means that all segments were actually directories
        return $segments;
    }

    /**
     * Set Directory, Controller, Method and params by segments
     * @param array $segments
     */
    protected function processSegments(array $segments)
    {
        $segments = $this->shiftDirectoriesFromSegments($segments);
        if (empty($segments)) {
            $this->setController(null);
            $this->setControllerMethod(null);
            $this->setControllerMethodParams(null);
            return;
        }

        $controllerName = array_shift($segments);
        $this->setController($this->translateUriPart($controllerName));

        if (empty($segments)) {
            $this->setControllerMethod(null);
            $this->setControllerMethodParams(null);
            return;
        }

        $controllerMethodName = array_shift($segments);
        $this->setControllerMethod($this->translateUriPart($controllerMethodName));

        if (empty($segments)) {
            $this->setControllerMethodParams(null);
            return;
        }

        $this->setControllerMethodParams($segments);
    }

    /**
     * @param string $uriPart
     * @return string
     */
    protected function translateUriPart($uriPart)
    {
        $find = [];
        $replace = [];
        foreach ($this->config->translateUri as $key => $value) {
            $find[] = $key;
            $replace[] = $value;
        }
        return ucfirst(str_replace($find, $replace, $uriPart));
    }
}

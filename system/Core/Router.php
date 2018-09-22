<?php

namespace CodeHuiter\Core;

use CodeHuiter\Exceptions\CoreCodeHuiterException;
use CodeHuiter\Exceptions\InvalidConfigException;
use CodeHuiter\Exceptions\InvalidRequestException;

class Router
{
    const SERVICE_KEY = 'router';
    const CONFIG_KEY = 'router';

    /** @var Request $request */
    protected $request;

    /** @var array */
    protected $config;

    /** @var string $directory */
    protected $directory;

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

    /** @var Benchmark $benchmark  */
    protected $benchmark;

    /**
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->config = $app->getConfig(self::CONFIG_KEY);
        $this->app = $app;
        $this->request = $app->get(Request::SERVICE_KEY);
        $this->benchmark = $app->get(Benchmark::SERVICE_KEY);

        $segments = $this->checkRoutingRewriteSegments();
        if ($segments === null) {
            $segments = $this->request->segments;
        }
        $this->processSegments($segments);
    }

    /**
     * @throws CoreCodeHuiterException
     */
    public function execute() {

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
                $this->benchmark->mark('CreateController');
                $this->controllerInstance = new $this->controller($this->app);
                $this->benchmark->mark('RunController');
                $this->controllerInstance->{$this->controllerMethod}(...$this->controllerMethodParams);

            } catch (\ReflectionException $exception) {
                throw new InvalidRequestException(
                    "Catching ReflectionMethodException for class '{$this->controller}' and method {$this->controllerMethod} "
                );
            }
        } catch (InvalidRequestException $exception) {
            if ($this->controller === $this->config['error_404']['controller']) {
                throw new CoreCodeHuiterException(
                    "Can't found '{$this->controller}::{$this->controllerMethod}' for call error 404",
                    0,
                    $exception
                );
            }
            $this->setController($this->config['error_404']['controller'], true);
            $this->setControllerMethod($this->config['error_404']['controller_method']);
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
            !isset($this->config[$routeKey]['controller'])
            || !isset($this->config[$routeKey]['controller_method'])
        ) {
            throw new InvalidConfigException("Not correct exist config.router.{$routeKey} ");
        }
        $this->setController($this->config[$routeKey]['controller'], true);
        $this->setControllerMethod($this->config[$routeKey]['controller_method']);
        $this->setControllerMethodParams($params);
    }

    /**
     * @param string $dir
     * @param bool $append
     */
    protected function setDirectory(string $dir, $append = false)
    {
        if ($append !== true || empty($this->directory)) {
            $this->directory = str_replace('.', '', $dir) . '/';
        } else {
            $this->directory .= str_replace('.', '', $dir) . '/';
        }
    }

    /**
     * @param string|null $controllerName
     * @param bool $isFullName
     */
    protected function setController($controllerName = null, $isFullName = false)
    {
        if ($controllerName) {
            if ($isFullName) {
                $this->controller = $controllerName;
            } else {
                $this->controller =
                    '\\App\\'
                    . str_replace('/', '\\', $this->directory)
                    . $controllerName . '_Controller';
            }
        } else {
            $this->controller = $this->config['default']['controller'];
        }
    }

    /**
     * @param string|null $controllerMethodName
     */
    protected function setControllerMethod($controllerMethodName = null)
    {
        $this->controllerMethod = ($controllerMethodName)
            ? $controllerMethodName : $this->config['default']['controller_method'];
    }

    /**
     * @param array|null $controllerMethodParams
     */
    protected function setControllerMethodParams($controllerMethodParams = null)
    {
        $this->controllerMethodParams = ($controllerMethodParams)
            ? $controllerMethodParams : [];

        foreach($this->controllerMethodParams as $key => $value) {
            $this->controllerMethodParams[$key] =  urldecode($value);
        }
    }

    /**
     * Check request uri segments for match routes config.
     * If yes return new segments
     * @return array|null
     */
    protected function checkRoutingRewriteSegments()
    {
        $uri = implode('/', $this->request->segments);
        $http_verb = $this->request->method;

        $routes = $this->config['routes'];
        if (isset($this->config['domain_routes'][$this->request->domain])) {
            $routes = array_merge($routes, $this->config['domain_routes'][$this->request->domain]);
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
                if (strpos($segmentTest, 'MODULE_') === 0) {
                    $moduleName = substr($segmentTest, strlen('MODULE_'));
                    $this->setDirectory('Modules/'.$moduleName.'/Controllers', false);
                    array_shift($segments);
                    continue;
                } else {
                    $this->setDirectory('Controllers', false);
                }
            }
            $nameTest = $this->directory . $segmentTest;

            if (
                !file_exists(APP_PATH . $nameTest . '_Controller.php')
                && is_dir(APP_PATH . $nameTest)
            ) {
                array_shift($segments);
                $this->setDirectory($segmentTest, true);
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
        foreach ($this->config['translate_uri'] as $key => $value) {
            $find[] = $key;
            $replace[] = $value;
        }
        return ucfirst(str_replace($find, $replace, $uriPart));
    }
}

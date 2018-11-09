<?php

namespace CodeHuiter\Core;

use CodeHuiter\Config\Config;
use CodeHuiter\Exceptions\InvalidRequestException;
use CodeHuiter\Exceptions\ServerConfigException;

class Request
{
    /** @var array $segments */
    public $segments;

    public $protocol;

    public $method;

    public $domain;

    public $port;

    public $uri;

    protected $config;

    /**
     * @param Application $application
     * @throws InvalidRequestException
     * @throws ServerConfigException
     */
    public function __construct(Application $application)
    {
        $this->config = $application->getConfig(Config::CONFIG_KEY_REQUEST);
        $this->initialize();
        $this->checkServer();

        $this->segments = $this->parseRequestUri();

        $this->domain = $_SERVER['HTTP_HOST'] ?? 'cli';
        $this->uri = $_SERVER['REQUEST_URI'] ?? ('/' . implode('/', $this->segments));

        $this->method = isset($_SERVER['REQUEST_METHOD']) ? strtolower($_SERVER['REQUEST_METHOD']) : 'cli';
        //$body = file_get_contents('php://input');
    }

    /**
     * @return bool
     */
    public function isCli()
    {
        return (PHP_SAPI === 'cli' OR defined('STDIN'));
    }

    /**
     * @return bool
     */
    public function isAJAX(): bool
    {
        return ( ! empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
    }

    /**
     * @return bool
     */
    public function isMjsaAJAX(): bool
    {
        return (
            !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
            && ($this->getRequestValue('mjsaAjax') || $this->getRequestValue('bodyAjax'))
        );
    }

    /**
     * @return bool
     */
    public function isBodyAJAX(): bool
    {
        return (
            !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
            && $this->getRequestValue('bodyAjax')
        );
    }

    /**
     * @return bool
     */
    public function isSecure(): bool
    {
        if ( ! empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') {
            return true;
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
            return true;
        } elseif ( ! empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off') {
            return true;
        }
        return false;
    }

    public function getGet($key, $default = '')
    {
        $value = $this->getGlobal(INPUT_GET, $key);
        if ($value === null) {
            return $default;
        }
        return $value;
    }

    public function getPost($key, $default = '')
    {
        $value = $this->getGlobal(INPUT_POST, $key);
        if ($value === null) {
            return $default;
        }
        return $value;
    }

    public function getRequestValue($key, $default = '')
    {
        $value = $this->getGlobal(INPUT_POST, $key);
        if ($value === null) {
            $value = $this->getGlobal(INPUT_GET, $key);
        }
        if ($value === null) {
            return $default;
        }
        return $value;
    }


    public function getCookie($key, $default = '')
    {
        $value = $this->getGlobal(INPUT_COOKIE, $key);
        if ($value === null) {
            return $default;
        }
        return $value;
    }


    public function getClientIP()
    {
        return $this->getGlobal(INPUT_SERVER, 'REMOTE_ADDR', '');
    }

    protected function getGlobal($type, $key, $default = null)
    {
        $source = [];
        switch ($type) {
            case INPUT_GET : $source = $_GET;
                break;
            case INPUT_POST : $source = $_POST;
                break;
            case INPUT_COOKIE : $source = $_COOKIE;
                break;
            case INPUT_SERVER : $source = $_SERVER;
                break;
            case INPUT_ENV : $source = $_ENV;
                break;
            case INPUT_REQUEST : $source = $_REQUEST;
                break;
        }

        $value = $source[$key] ?? $default;

        return $value !== null ? filter_var($value, FILTER_DEFAULT) : $value;
    }

    /**
     * Will parse the REQUEST_URI and automatically detect the URI from it,
     * fixing the query string if necessary.
     *
     * @return array
     * @throws InvalidRequestException
     */
    protected function parseRequestUri()
    {
        if ( ! isset($_SERVER['REQUEST_URI'], $_SERVER['SCRIPT_NAME'])) {
            return [];
        }

        // parse_url() returns false if no host is present, but the path or query string
        // contains a colon followed by a number
        $parts = parse_url('http://dummy' . $_SERVER['REQUEST_URI']);
        $query = $parts['query'] ?? '';
        $uri = $parts['path'] ?? '';

        if (isset($_SERVER['SCRIPT_NAME'][0])) {
            if (strpos($uri, $_SERVER['SCRIPT_NAME']) === 0) {
                $uri = (string) substr($uri, strlen($_SERVER['SCRIPT_NAME']));
            } elseif (strpos($uri, dirname($_SERVER['SCRIPT_NAME'])) === 0) {
                $uri = (string) substr($uri, strlen(dirname($_SERVER['SCRIPT_NAME'])));
            }
        }

        // This section ensures that even on servers that require the URI to be in the query string (Nginx) a correct
        // URI is found, and also fixes the QUERY_STRING getServer var and $_GET array.
        if (trim($uri, '/') === '' && strncmp($query, '/', 1) === 0) {
            $query = explode('?', $query, 2);
            $uri = $query[0];
            $_SERVER['QUERY_STRING'] = $query[1] ?? '';
        } else {
            $_SERVER['QUERY_STRING'] = $query;
        }
        parse_str($_SERVER['QUERY_STRING'], $_GET);

        if ($uri === '/' || $uri === '') {
            return [];
        }

        return $this->createSegments($uri);
    }

    /**
     * @param string $uri
     * @return array
     * @throws InvalidRequestException
     */
    protected function createSegments($uri)
    {
        $uris = [];
        $tok = strtok($uri, '/');
        while ($tok !== false) {
            if (( ! empty($tok) || $tok === '0') && $tok !== '..') {

                $uriSegment = trim(remove_invisible_characters($tok, FALSE));
                if (
                    !empty($uriSegment)
                    && !empty($this->config['permitted_uri_chars'])
                    && !preg_match(
                        '/^['.$this->config['permitted_uri_chars'].']+$/iu',
                        $uriSegment
                    )
                ) {
                    throw new InvalidRequestException('The URI you submitted has disallowed characters.');
                }
                if ($uriSegment !== '') {
                    $uris[] = $uriSegment;
                }
            }
            $tok = strtok('/');
        }
        return $uris;
    }

    protected $charset = 'UTF-8';
    protected $string_mb_enabled = false;
    protected $string_iconv_enabled = false;

    protected function initialize()
    {
        $charset = strtoupper($this->charset);
        ini_set('default_charset', $charset);

        if (extension_loaded('mbstring'))
        {
            $this->string_mb_enabled = true;
            define('MB_ENABLED', TRUE);
            // This is required for mb_convert_encoding() to strip invalid characters.
            // That's utilized by CI_Utf8, but it's also done for consistency with iconv.
            mb_substitute_character('none');
        }

        // There's an ICONV_IMPL constant, but the PHP manual says that using
        // iconv's predefined constants is "strongly discouraged".
        if (extension_loaded('iconv')) {
            $this->string_iconv_enabled = true;
        }
        ini_set('php.internal_encoding', $charset);
    }

    /**
     * @throws ServerConfigException
     */
    protected function checkServer()
    {
        // @todo Server min php7 require
        if (!$this->string_iconv_enabled && !$this->string_mb_enabled) {
            throw new ServerConfigException('Server not support iconv or mbstring');
        }
    }
}

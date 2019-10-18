<?php

namespace CodeHuiter\Core\ByDefault;

use CodeHuiter\Service\MimeTypeConverter;
use CodeHuiter\Config\ResponseConfig;
use CodeHuiter\Core\Application;
use CodeHuiter\Core\CodeLoader;
use CodeHuiter\Core\Exception\ExceptionProcessor;
use CodeHuiter\Core\Request;
use CodeHuiter\Exception\InvalidConfigException;

class Response implements \CodeHuiter\Core\Response
{
    /** @var array */
    protected $finalHeaders = [];

    /** @var string */
    protected $finalOutput;

    /**
     * @var ResponseConfig
     */
    protected $config;

    protected $initLevel;

    protected $controller;

    /** @var Request */
    protected $request;

    protected $app;

    /**
     * @param Application $app
     * @param ResponseConfig $config
     * @param Request $request
     */
    public function __construct(Application $app, ResponseConfig $config, Request $request)
    {
        $this->initLevel = ob_get_level();
        $this->app = $app;
        $this->config = $config;
        $this->request = $request;
    }

    public function append(string $output): void
    {
        $this->finalOutput .= $output;
    }

    /**
     * @param array $headerStrings
     */
    public function setHeaders(array $headerStrings): void
    {
        foreach ($headerStrings as $key => $headerString) {
            if (is_string($key)) {
                $this->finalHeaders[$key] = [$headerString];
            } else {
                $headerArr = explode(':', $headerString);
                $this->finalHeaders[trim($headerArr[0])] = [$headerString];
            }
        }
    }

    public function denyIframe(): void
    {
        $this->setHeaders(['X-Frame-Options: DENY']);
    }

    /**
     * @param $extensionOrFilename
     * @param string | null $charset null - Not send charset, 'default' - default response charset
     */
    public function setMimeType(string $extensionOrFilename, ? string$charset = 'default'): void
    {
        /** @var \CodeHuiter\Service\MimeTypeConverter $mimeTypeConverter */
        $mimeTypeConverter = $this->app->get(MimeTypeConverter::class);
        if ($charset === 'default') {
            $charset = $this->config->charset;
        }
        $this->setHeaders([$mimeTypeConverter->getTypeHeader($extensionOrFilename, $charset)]);
    }

    public function setCookie(string $name, string $value, int $expireTime, string $path, string $domain): void
    {
        setcookie($name, $value, $expireTime, $path, $domain);
    }

    private function sendHeaders(): void
    {
        foreach ($this->finalHeaders as $header) {
            header($header);
        }
        $this->finalHeaders = [];
    }


    public function send(): void
    {
        $this->sendHeaders();
        if ($this->config->profiler) {
            /** @var CodeLoader $loader */
            $loader = $this->app->get(CodeLoader::class);
            $loader->benchmarkPoint('ResponseSend');
            if (strpos($this->finalOutput, '{#result_time_table}') !== false) {
                $this->finalOutput = str_replace('{#result_time_table}', $loader->benchmarkTotalTimeTable(), $this->finalOutput);
            }
            if (strpos($this->finalOutput, '{#result_class_table}') !== false) {
                $this->finalOutput = str_replace('{#result_class_table}', $loader->benchmarkTotalLoadedTable(), $this->finalOutput);
            }
            if (strpos($this->finalOutput, '{#result_time}') !== false) {
                $this->finalOutput = str_replace('{#result_time}', $loader->benchmarkElapsedString('BEFORE_SEND_RESPONSE'), $this->finalOutput);
            }
            if (strpos($this->finalOutput, '{#result_memory}') !== false) {
                $this->finalOutput = str_replace('{#result_memory}', $loader->benchmarkTotalMemoryString(), $this->finalOutput);
            }
        }

        echo $this->finalOutput;
    }

    /**
     * * @todo set and replace any uses 'set_status_header'
     *
     * @param int $code
     * @param string|null $text
     */
    public function setStatus(int $code, ?string $text = null): void
    {
        if ($this->request->isCli()) {
            return;
        }

        if ($text === null) {
            $code = (int) $code;

            if (isset(\CodeHuiter\Core\Response::HTTP_CODES[$code])) {
                $text = \CodeHuiter\Core\Response::HTTP_CODES[$code];
            } else {
                ExceptionProcessor::defaultProcessException(new InvalidConfigException(
                    'No status text available. Please check your status code number or supply your own message text.'
                ));
            }
        }

        if (strpos(PHP_SAPI, 'cgi') === 0) {
            header('Status: '.$code.' '.$text, TRUE);
        } else {
            $server_protocol = (isset($_SERVER['SERVER_PROTOCOL']) && in_array($_SERVER['SERVER_PROTOCOL'], array('HTTP/1.0', 'HTTP/1.1', 'HTTP/2'), TRUE))
                ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1';
            header($server_protocol.' '.$code.' '.$text, TRUE, $code);
        }
    }

    /**
     * Redirect on other page
     * @param string $url
     * @param boolean $temperatory Is 302 Moved Temperatory
     */
    public function location(string $url, bool $temperatory = false): void
    {
        if($temperatory === true){
            header("HTTP/1.0 302 Moved Temporarily");
        } else {
            header("HTTP/1.0 301 Moved Permanently");
        }
        header('Location: '.$url);
    }
}

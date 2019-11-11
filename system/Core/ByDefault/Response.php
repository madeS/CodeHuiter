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
    /** @var array */
    protected $finalCookies = [];

    /** @var string */
    protected $finalOutput = '';

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
     * @param string[] $headerStrings
     * @param bool $replace
     * @param int|null $code
     */
    public function setHeaders(array $headerStrings, bool $replace = false, ?int $code = null): void
    {
        foreach ($headerStrings as $key => $headerString) {
            $this->finalHeaders[$key] = [$headerString, $replace, $code];
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
        $this->finalCookies[] = [$name, $value, $expireTime, $path, $domain];
    }

    private function sendHeaders(): void
    {
        foreach ($this->finalHeaders as $header) {
            [$headerContent, $replace, $code] = $header;
            header($headerContent, $replace, $code);
        }
        $this->finalHeaders = [];
    }

    private function sendCookies(): void
    {
        foreach ($this->finalCookies as $finalCookie) {
            [$name, $value, $expireTime, $path, $domain] = $finalCookie;
            setcookie($name, $value, $expireTime, $path, $domain);
        }
    }


    public function send(): void
    {
        $this->sendHeaders();
        $this->sendCookies();
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
     * @param int $code
     * @param string|null $text
     */
    public function setStatus(int $code, ?string $text = null): void
    {
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

        if ($this->request->isCli()) {
            $this->setHeaders(["Status: $code $text"], true, $code);
        } else {
            $protocol = $this->request->getProtocol();
            $this->setHeaders(["$protocol $code $text"], true, $code);
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
            $this->setStatus(302, 'Moved Temporarily');
        } else {
            $this->setStatus(301, 'Moved Permanently');
        }
        $this->setHeaders(["Location: $url"], true);
    }

    /**
     * @return string[]
     */
    public function getHeaders(): array
    {
        return $this->finalHeaders;
    }

    public function getCookies(): array
    {
        return $this->finalCookies;
    }

    public function getContent(): string
    {
        return $this->finalOutput;
    }
}

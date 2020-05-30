<?php

namespace CodeHuiter\Service\ByDefault;

use CodeHuiter\Config\Service\RendererConfig;
use CodeHuiter\Core\Controller;
use CodeHuiter\Core\Exception\ExceptionProcessor;
use CodeHuiter\Core\Response;
use CodeHuiter\Exception\InvalidConfigException;
use CodeHuiter\Modifier\StringModifier;
use CodeHuiter\Service\Logger;
use CodeHuiter\Service\Renderer;

class PhpRenderer implements Renderer
{
    /**
     * @var RendererConfig
     */
    protected $config;
    /**
     * @var Response
     */
    protected $response;
    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var int
     */
    protected $initLevel;
    /**
     * @var Controller
     */
    protected $controller;

    /**
     * @var array
     */
    protected $cachedData = [];

    public function __construct(RendererConfig $config, Response $response, Logger $logger)
    {
        $this->initLevel = ob_get_level();
        $this->config = $config;
        $this->response = $response;
        $this->logger = $logger;
    }

    public function getInitLevel(): int
    {
        return $this->initLevel;
    }

    /**
     * TODO Implement UnitTest
     * {@inheritDoc}
     */
    public function render(string $viewFile, array $data = [], bool $return = false): string
    {
        $this->controller = Controller::getInstance();

        $_view_file = $viewFile;
        if (strpos($viewFile,':') === 0) {
            $_view_file = VIEW_PATH . substr($viewFile, 1);
        }

        if (strpos($_view_file, $this->config->templateNameAppend) === false) {
            $_view_file .= $this->config->templateNameAppend;
        }

        if (!file_exists($_view_file)) {
            ExceptionProcessor::defaultProcessException(
                new InvalidConfigException('Template [' . $_view_file . '] not found to process')
            );
        }

        if (!empty($data)) {
            $this->cachedData = array_merge($this->cachedData, $data);
        }

        /**
         * View Data
         */
        extract($this->cachedData, EXTR_OVERWRITE);
        /** @noinspection PhpUnusedLocalVariableInspection */
        $those = $this->controller;
        /** @noinspection PhpUnusedLocalVariableInspection */
        $renderer = $this;

        ob_start();

        /** @noinspection PhpIncludeInspection */
        include $_view_file;

        if ($return === true) {
            $buffer = ob_get_contents();
            @ob_end_clean();
            return $buffer;
        }

        /*
         * Flush the buffer... or buff the flusher?
         *
         * In order to permit views to be nested within
         * other views, we need to flush the content back out whenever
         * we are beyond the first level of output buffering so that
         * it can be seen and included properly by the first included
         * template and any subsequent ones. Oy!
         */
        if (ob_get_level() > $this->initLevel + 1) {
            ob_end_flush();
        } else {
            if ($this->response !== null) {
                $content = ob_get_contents();
                $this->response->append($content);
            } else {
                $this->logger->withTag('PHP_RENDERER')->withTrace()->notice('Rendering without Response');
                echo ob_get_contents();
            }
            @ob_end_clean();
        }
        return '';
    }

    /**
     * Html Encode (HtmlSpecialChars)
     * @param string $string
     * @return string
     */
    public function textForHtml(string $string): string
    {
        return StringModifier::textForHtml($string);
    }
}

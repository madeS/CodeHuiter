<?php

namespace CodeHuiter\Config\Service;

use CodeHuiter\Config\Core\ServiceConfig;
use CodeHuiter\Config\CoreConfig;
use CodeHuiter\Core\Application;
use CodeHuiter\Core\Response;
use CodeHuiter\Service\ByDefault\PhpRenderer;
use CodeHuiter\Service\Logger;
use CodeHuiter\Service\Renderer;

class RendererConfig
{
    /** @var string */
    public $templateNameAppend = '.tpl.php';

    public static function populateConfig(CoreConfig $config): void
    {
        $config->rendererConfig = new self();
        $config->services[Renderer::class] = ServiceConfig::forCallback(
            static function (Application $app) {
                return $app->get(PhpRenderer::class);
            },
            ServiceConfig::SCOPE_REQUEST
        );
        $config->services[PhpRenderer::class] = ServiceConfig::forCallback(
            static function (Application $app) {
                return new PhpRenderer(
                    $app->config->rendererConfig,
                    $app->get(Response::class),
                    $app->get(Logger::class)
                );
            },
            ServiceConfig::SCOPE_REQUEST
        );
    }
}
<?php

namespace CodeHuiter\Config\Core;

use CodeHuiter\Config\CoreConfig;
use CodeHuiter\Core\Application;
use CodeHuiter\Core\Request;
use CodeHuiter\Core\Response;
use CodeHuiter\Core\ByDefault;

class ResponseConfig
{
    /** @var string */
    public $charset = 'UTF-8'; // Recommended
    /**
     * Placeholders:
     *   {#result_time_table}
     *   {#result_class_table}
     *   {#result_time}
     *   {#result_memory}
     * @var bool
     */
    public $profiler = true;

    public static function populateConfig(CoreConfig $config): void
    {
        $config->responseConfig = new self();
        $config->services[Response::class] = ServiceConfig::forCallback(
            static function (Application $app) {
                return new ByDefault\Response(
                    $app,
                    $app->config->responseConfig,
                    $app->get(Request::class)
                );
            },
            ServiceConfig::SCOPE_REQUEST
        );
    }
}
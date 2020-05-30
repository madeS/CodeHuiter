<?php

namespace CodeHuiter\Config\Facilities\Service;

use CodeHuiter\Config\Core\ServiceConfig;
use CodeHuiter\Config\FacilitiesConfig;
use CodeHuiter\Core\Application;
use CodeHuiter\Core\Request;
use CodeHuiter\Facilities\Service\AjaxResponse;
use CodeHuiter\Facilities\Service\ByDefault\JsonAjaxResponse;
use CodeHuiter\Facilities\Service\ByDefault\MjsaAjaxResponse;
use CodeHuiter\Service\Language;

class AjaxResponseConfig
{
    public static function populateConfig(FacilitiesConfig $config): void
    {
        $config->services[AjaxResponse::class] = ServiceConfig::forCallback(
            static function (Application $app) {
                /** @var Request $request */
                $request = $app->get(Request::class);
                if ($request->getRequestValue('mjsaAjax') || $request->getRequestValue('bodyAjax')) {
                    return new MjsaAjaxResponse($app->get(Language::class));
                }
                if ($request->getRequestValue('jsonAjax') || $request->getRequestValue('bodyJsonAjax')) {
                    return new JsonAjaxResponse($app->get(Language::class));
                }
                return new JsonAjaxResponse($app->get(Language::class));
            },
            ServiceConfig::SCOPE_REQUEST
        );
    }
}
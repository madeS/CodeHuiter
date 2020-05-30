<?php

namespace CodeHuiter\Config\Facilities\Service;

use CodeHuiter\Config\Core\ServiceConfig;
use CodeHuiter\Config\FacilitiesConfig;
use CodeHuiter\Core\Application;
use CodeHuiter\Core\Request;
use CodeHuiter\Facilities\Service\Compressor;
use CodeHuiter\Service\ByDefault\PhpRenderer;

class CompressorConfig
{
    public $version = 'dev'; // OR some time for cache
    public $dir = '/pub/compressor';
    public $names = 'compressed';
    public $css = [
        //'http://code.jquery.com/ui/1.9.2/themes/base/jquery-ui.css',
        '/pub/css/mjsa.css',
        '/pub/plugins/jqueryui/jquery-ui.min.css',
    ];
    public $js = [
        '/pub/js/jquery-3.1.1.min.js',
        '/pub/plugins/jqueryui/jquery-ui.min.js',
        '/pub/js/mjsa.js',
    ];
    public $resultCss = '';
    public $resultJs = '';
    public $singlyCss = [];
    public $singlyJs = [];
    public $domainCompressor = ['sub.app.local' => null];

    public static function populateConfig(FacilitiesConfig $config): void
    {
        $config->compressorConfig = new self();
        $config->services[Compressor::class] = ServiceConfig::forCallback(
            static function (Application $app) {
                return new \CodeHuiter\Facilities\Service\ByDefault\Compressor(
                    $app->config->compressorConfig,
                    $app->get(Request::class),
                    $app->get(PhpRenderer::class)
                );
            },
            ServiceConfig::SCOPE_REQUEST
        );

        // connect image crop (jcrop)
        $config->compressorConfig->css[] = '/pub/css/jquery.jcrop.min.css';
        $config->compressorConfig->js[] = '/pub/js/jquery.jcrop.min.js';
        // connect audio (jplayer)
        $config->compressorConfig->js[] = '/pub/js/jplayer/jquery.jplayer.min.js';
        // fancybox
        $config->compressorConfig->singlyCss[] = '/pub/plugins/fancybox/jquery.fancybox.css';
        $config->compressorConfig->singlyJs['fancybox'] = '/pub/plugins/fancybox/jquery.fancybox.pack.js';
        // select2
        $config->compressorConfig->singlyCss[] = '/pub/plugins/select2/select2.css';
        $config->compressorConfig->singlyJs['select2'] = '/pub/plugins/select2/select2.js';
        // tiny
        $config->compressorConfig->singlyJs['tinymce'] = '/pub/plugins/tinymce/tinymce.min.js';
        // application js
        $config->compressorConfig->css[] = '/pub/css/app.css.tpl.php';
        $config->compressorConfig->js[] = '/pub/js/app.js';
        // app.jplayer
        $config->compressorConfig->js[] = '/pub/js/app.jplayer.js';
        // app.dialogues
        $config->compressorConfig->css[] = '/pub/css/app.dialogues.css';
        $config->compressorConfig->js[] = '/pub/js/app.dialogues.js';
        // app.comments
        $config->compressorConfig->css[] = '/pub/css/app.comments.css';
        $config->compressorConfig->js[] = '/pub/js/app.comments.js';
        // app.custom
        $config->compressorConfig->js[] = '/pub/js/app.custom.js';
        // yashare
        $config->compressorConfig->singlyJs['yashare'] = '//yastatic.net/share/share.js" charset="utf-8';
        $config->compressorConfig->js[] = '/pub/js/app.yashare.js';
    }
}
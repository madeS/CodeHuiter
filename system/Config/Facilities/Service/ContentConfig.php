<?php

namespace CodeHuiter\Config\Facilities\Service;

use CodeHuiter\Config\Core\ServiceConfig;
use CodeHuiter\Config\FacilitiesConfig;
use CodeHuiter\Core\Application;
use CodeHuiter\Facilities\Service\Content;
use CodeHuiter\Service\FileStorage;
use CodeHuiter\Service\Logger;

class ContentConfig
{
    public $storageMap = [
        'watermarks' => [
            'store' => '/pub/images/watermarks/',
            'server_root' => PUB_PATH,
            'site_url' => '',
        ],
        'user_medias' => [
            'store' => '/pub/files/images/user_medias/',
            'server_root' => PUB_PATH,
            'site_url' => '',
        ],
        'example_cloud' => [
            'store' => '/cloud_folder/{#locale}/',
            'server_root' => '/home/disk1',
            'site_url' => 'http://asset.example.com',
        ],
    ];

    public static function populateConfig(FacilitiesConfig $config): void
    {
        $config->contentConfig = new self();
        $config->services[Content::class] = ServiceConfig::forCallback(
            static function (Application $app) {
                return new Content(
                    $app->config->contentConfig,
                    $app->get(FileStorage::class),
                    $app->get(Logger::class)
                );
            }
        );
    }
}
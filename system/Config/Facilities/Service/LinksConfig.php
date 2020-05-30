<?php

namespace CodeHuiter\Config\Facilities\Service;

use App\Service\Links;
use CodeHuiter\Config\Core\ServiceConfig;
use CodeHuiter\Config\FacilitiesConfig;
use CodeHuiter\Core\Application;

class LinksConfig
{
    public $aliases = [
        'users' => '/users',
        'user_view' => '/users/id{#param}',
        'user_settings' => '/users/settings',
        'user_medias' => '/users/id{#param}/medias',
        'messages' => '/messages',
        'messages_user' => '/messages/user{#param}',
        'messages_room' => '/messages/room{#param}',

        'blog_add' => '/blog/add',
        'blog_edit' => '/blog/edit/{#param}',
        'blog_page_categored' => '/page-{#param}/{#param}',
        'blog_page' => '/page-{#param}',

        'user_albums' => '/users/id{#param}/albums',
        'user_album' => '/users/id{#param}/album{#param}',
        'user_album_edit' => '/users/id{#param}/album{#param}/edit',
    ];

    public static function populateConfig(FacilitiesConfig $config): void
    {
        $config->linksConfig = new self();
        $config->services[Links::class] = ServiceConfig::forCallback(
            static function (Application $app) {
                return new Links($app, $app->config->linksConfig);
            }
        );
    }
}
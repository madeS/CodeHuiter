<?php

namespace CodeHuiter\Config\Service;

use CodeHuiter\Config\Core\ServiceConfig;
use CodeHuiter\Config\CoreConfig;
use CodeHuiter\Service\EventDispatcher;

class EventsConfig
{
    public $events = [];

    public static function modelUpdated(string $class)
    {
        return $class . '.updated';
    }

    public static function modelDeleting(string $class)
    {
        return $class . '.deleting';
    }

    public static function populateConfig(CoreConfig $config): void
    {
        $config->eventsConfig = new self();
        $config->services[EventDispatcher::class] = ServiceConfig::forAppClass(
            \CodeHuiter\Service\ByDefault\EventDispatcher\EventDispatcher::class
        );
    }
}
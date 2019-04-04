<?php

namespace CodeHuiter\Core\Event;

interface ApplicationEventSubscriber
{
    public function catchEvent(ApplicationEvent $event): void;
}

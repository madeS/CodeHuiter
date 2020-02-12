<?php

namespace CodeHuiter\Service\ByDefault\EventDispatcher;

use CodeHuiter\Core\Application;
use CodeHuiter\Exception\InvalidFlowException;
use CodeHuiter\Service\EventDispatcher\Event;
use CodeHuiter\Service\EventDispatcher\EventSubscriber;

class EventDispatcher implements \CodeHuiter\Service\EventDispatcher
{
    /**
     * @var Application
     */
    private $application;

    /**
     * @var string[][] <EventName, Index, ServiceName>
     */
    protected $subscriptions = [];

    public function __construct(Application $application)
    {
        $this->application = $application;
        $events = $this->application->config->eventsConfig->events;
        foreach ($events as $event) {
            $this->subscribe($event[0], $event[1]);
        }
    }

    public function subscribe(string $eventName, string $subscriberServiceName): void
    {
        if (!isset($this->subscriptions[$eventName])) {
            $this->subscriptions[$eventName] = [];
        }
        $this->subscriptions[$eventName][] = $subscriberServiceName;
    }

    public function fire(Event $event): void
    {
        $eventName = $event->getEventName();
        if (!isset($this->subscriptions[$eventName])) {
            return;
        }
        foreach ($this->subscriptions[$eventName] as $subscriberServiceName) {
            $subscriber = $this->application->get($subscriberServiceName);
            if (!$subscriber instanceof EventSubscriber) {
                throw InvalidFlowException::onAnotherClassExpected(EventSubscriber::class, get_class($subscriber));
            }
            $subscriber->catchEvent($event);
        }
    }
}

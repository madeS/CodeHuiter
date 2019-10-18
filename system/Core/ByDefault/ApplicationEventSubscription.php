<?php

namespace CodeHuiter\Core\ByDefault;

use CodeHuiter\Core\Application;
use CodeHuiter\Core\Event\ApplicationEventSubscriber;
use CodeHuiter\Exception\Runtime\EventException;

class ApplicationEventSubscription
{
    /**
     * @var int
     */
    public $priority;

    /**
     * @var mixed
     */
    private $subscriber;

    /**
     * @param mixed $subscriber
     * @param int $priority
     */
    public function __construct($subscriber, int $priority)
    {
        $this->subscriber = $subscriber;
        $this->priority = $priority;
    }

    /**
     * @param Application $application
     * @param string $eventClass
     * @return ApplicationEventSubscriber
     */
    public function getSubscriber(Application $application, string $eventClass): ApplicationEventSubscriber
    {
        if ($this->subscriber instanceof ApplicationEventSubscriber) {
            return $this->subscriber;
        }
        if ($application->serviceExist($this->subscriber)) {
            return $application->get($this->subscriber);
        }
        if (class_exists($this->subscriber)) {
            $class = $this->subscriber;
            return new $class();
        }
        throw EventException::onInvalidSubscriber($eventClass, get_class($this->subscriber));
    }
}
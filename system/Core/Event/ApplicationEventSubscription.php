<?php

namespace CodeHuiter\Core\Event;

use CodeHuiter\Core\Application;
use CodeHuiter\Exceptions\InvalidFlowException;

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
     * @param Application|null $application
     * @return ApplicationEventSubscriber
     */
    public function getSubscriber(Application $application): ?ApplicationEventSubscriber
    {
        if ($this->subscriber instanceof ApplicationEventSubscriber) {
            return $this->subscriber;
        }
        if ($application->serviceExist($this->subscriber)) {
            return $application->get($this->subscriber);
        } elseif (class_exists($this->subscriber)) {
            $class = $this->subscriber;
            return new $class();
        } else {
            $application->fireException(new InvalidFlowException(sprintf(
                'Invalid subscriber [%s]',
                $this->subscriber
            )));
            return null;
        }
    }
}
<?php

namespace Resources\Application\Event;

class Dispatcher
{

    /**
     *
     * @var bool
     */
    protected $strongConsistencyMode;

    /**
     * 
     * @var Listener[]
     */
    protected $listeners = [];
    protected $dispatchedEvents = [];

    public function __construct(bool $strongConsistencyMode = true)
    {
        $this->strongConsistencyMode = $strongConsistencyMode;
        $this->listeners = [];
        $this->dispatchedEvents = [];
    }

    public function addListener(string $eventName, Listener $listener): void
    {
        $alreadyContainListener = isset($this->listeners[$eventName]) ?
                in_array($listener, $this->listeners[$eventName]) : false;

        if (!$alreadyContainListener) {
            $this->listeners[$eventName][] = $listener;
        }
    }

    public function dispatch(ContainEvents $containEvents): void
    {
        if (!$this->strongConsistencyMode) {
            foreach ($containEvents->pullRecordedEvents() as $event) {
                $this->dispatchedEvents[] = $event;
            }
        } else {
            foreach ($containEvents->pullRecordedEvents() as $event) {
                $this->publish($event);
            }
        }
    }

    public function execute()
    {
        foreach ($this->dispatchedEvents as $event) {
            $this->publish($event);
        }
    }

    private function publish(Event $event): void
    {
        if (!isset($this->listeners[$event->getName()])) {
            return;
        }

        foreach ($this->listeners[$event->getName()] as $listener) {
            $listener->handle($event);
        }
    }

}

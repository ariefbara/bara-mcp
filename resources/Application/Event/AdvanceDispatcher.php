<?php

namespace Resources\Application\Event;

class AdvanceDispatcher
{

    /**
     * 
     * @var Listener[]
     */
    protected $immediateListeners = [];

    /**
     * 
     * @var Listener[]
     */
    protected $postponedListeners = [];

    /**
     * 
     * @var Listener[]
     */
    protected $asynchronousListeners = [];

    /**
     * 
     * @var Event[]
     */
    public  $dispatchedEvents = [];
    /**
     * 
     * @var Event[]
     */
    public $asynchronousEvents = [];

    public function addImmediateListener(string $eventName, Listener $listener): void
    {
        $alreadyContainListener = isset($this->immediateListeners[$eventName]) ?
                in_array($listener, $this->immediateListeners[$eventName]) : false;
        if (!$alreadyContainListener) {
            $this->immediateListeners[$eventName][] = $listener;
        }
    }

    public function addPostponedListener(string $eventName, Listener $listener): void
    {
        $alreadyContainListener = isset($this->postponedListeners[$eventName]) ?
                in_array($listener, $this->postponedListeners[$eventName]) : false;
        if (!$alreadyContainListener) {
            $this->postponedListeners[$eventName][] = $listener;
        }
    }
    public function addAsynchronousListener(string $eventName, Listener $listener): void
    {
        $alreadyContainListener = isset($this->asynchronousListeners[$eventName]) ?
                in_array($listener, $this->asynchronousListeners[$eventName]) : false;
        if (!$alreadyContainListener) {
            $this->asynchronousListeners[$eventName][] = $listener;
        }
    }

    public function dispatch(ContainEvents $containEvents): void
    {
        foreach ($containEvents->pullRecordedEvents() as $event) {
            $this->dispatchedEvents[] = $event;
            $this->asynchronousEvents[] = $event;
            $this->publishEventToImmediateListeners($event);
        }
    }

    public function finalize(): void
    {
        foreach ($this->dispatchedEvents as $event) {
            $this->publishEventToPostponedListeners($event);
        }
    }

    public function finalizeAsynchronous(): void
    {
        foreach ($this->asynchronousEvents as $event) {
            $this->publishEventToAsynchronousListeners($event);
        }
    }

    private function publishEventToImmediateListeners(Event $event): void
    {
        if (!isset($this->immediateListeners[$event->getName()])) {
            return;
        }
        foreach ($this->immediateListeners[$event->getName()] as $listener) {
            $listener->handle($event);
        }
    }

    private function publishEventToPostponedListeners(Event $event): void
    {
        if (!isset($this->postponedListeners[$event->getName()])) {
            return;
        }
        foreach ($this->postponedListeners[$event->getName()] as $listener) {
            $listener->handle($event);
        }
    }

    private function publishEventToAsynchronousListeners(Event $event): void
    {
        if (!isset($this->asynchronousListeners[$event->getName()])) {
            return;
        }
        foreach ($this->asynchronousListeners[$event->getName()] as $listener) {
            $listener->handle($event);
        }
    }
    
    public function dispatchEvent(Event $event): void
    {
        $this->dispatchedEvents[] = $event;
        $this->asynchronousEvents[] = $event;
        $this->publishEventToImmediateListeners($event);
    }

}

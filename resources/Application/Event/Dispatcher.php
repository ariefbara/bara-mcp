<?php

namespace Resources\Application\Event;

class Dispatcher
{
    /**
     * 
     * @var Listener[]
     */
    protected $listeners = [];
    
    public function __construct() {
        
    }
    
    public function addListener(string $eventName, Listener $listener): void {
        $alreadyContainListener = isset($this->listeners[$eventName])? 
            in_array($listener, $this->listeners[$eventName]): false;
        
        if (!$alreadyContainListener) {
            $this->listeners[$eventName][] = $listener;
        }
    }
    
    public function dispatch(ContainEvents $containEvents): void {
        foreach ($containEvents->pullRecordedEvents() as $event) {
            $this->publish($event);
        }
    }
    
    private function publish(Event $event): void{
        if (!isset($this->listeners[$event->getName()])) {
            return;
        }
        
        foreach ($this->listeners[$event->getName()] as $listener) {
            $listener->handle($event);
        }
    }
}


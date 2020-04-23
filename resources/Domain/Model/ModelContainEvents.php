<?php

namespace Resources\Domain\Model;

use Resources\Application\Event\ {
    ContainEvents,
    Event
};

class ModelContainEvents implements ContainEvents
{
    
    protected $recordedEvents = [];
    
    protected function recordEvent(Event $event): void {
        $this->recordedEvents[] = $event;
    }
    
    /**
     * 
     * @return Event[]
     */
    public function getRecordedEvents(): array {
        return $this->recordedEvents;
    }
    
    public function clearRecordedEvents(): void {
        $this->recordedEvents = [];
    }

}

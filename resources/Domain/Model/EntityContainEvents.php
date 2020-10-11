<?php

namespace Resources\Domain\Model;

use Resources\Application\Event\{
    ContainEvents,
    Event
};

abstract class EntityContainEvents implements ContainEvents
{

    protected $recordedEvents = [];

    protected function recordEvent(Event $event): void
    {
        $this->recordedEvents[] = $event;
    }
    
    public function pullRecordedEvents(): array
    {
        $recordedEvents = $this->recordedEvents;
        $this->recordedEvents = [];
        return $recordedEvents;
    }

}

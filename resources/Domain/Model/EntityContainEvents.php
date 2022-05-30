<?php

namespace Resources\Domain\Model;

use Resources\Application\Event\ContainEvents;
use Resources\Application\Event\Event;

abstract class EntityContainEvents implements ContainEvents
{

    protected $recordedEvents = [];
    /**
     * 
     * @var EntityContainEvents[]
     */
    protected $aggregatedEventsFromBranches = [];

    protected function recordEvent(Event $event): void
    {
        $this->recordedEvents[] = $event;
    }
    
    public function pullRecordedEvents(): array
    {
        $recordedEvents = $this->recordedEvents;
        $this->recordedEvents = [];
        foreach ($this->aggregatedEventsFromBranches as $brach) {
            $recordedEvents = array_merge($recordedEvents, $brach->pullRecordedEvents());
        }
        return $recordedEvents;
    }
    
    protected function aggregateEventFrom(EntityContainEvents $other): void
    {
        foreach ($other->pullRecordedEvents() as $event) {
            $this->recordEvent($event);
        }
    }
    
    protected function aggregateEventsFromBranch(EntityContainEvents $branch): void
    {
        $this->aggregatedEventsFromBranches[] = $branch;
    }

}

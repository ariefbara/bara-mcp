<?php

namespace Resources\Domain\Model;

use Resources\Application\Event\ContainEvents;
use Resources\Application\Event\Event;

abstract class ContainAggregatedEntitiesHavingEvents implements ContainEvents
{
    /**
     * 
     * @var Event[]
     */
    protected $recordedEvents = [];
    /**
     * 
     * @var ContainEvents[]
     */
    protected $aggregatedEntitiesHavingEvents = [];
    
    public function pullRecordedEvents(): array
    {
        $recordedEvents = $this->recordedEvents;
        foreach ($this->aggregatedEntitiesHavingEvents as $entity) {
            $recordedEvents = array_merge($recordedEvents, $entity->pullRecordedEvents());
        }
        $this->aggregatedEntitiesHavingEvents = [];
        $this->recordedEvents = [];
        return $recordedEvents;
    }
    
    protected function recordEvent(Event $event): void
    {
        $this->recordedEvents[] = $event;
    }
    
    protected function recordEntityHavingEvents(ContainEvents $entity): void
    {
        $this->aggregatedEntitiesHavingEvents[] = $entity;
    }
}

<?php

namespace Resources\Domain\Model;

use Resources\ {
    Application\Event\ContainEvents,
    Domain\Event\CommonEvent
};

abstract class EntityContainCommonEvents implements ContainEvents
{
    protected $recordedEvents = [];
    
    protected function recordEvent(CommonEvent $commonEvent): void
    {
        $this->recordedEvents[] = $commonEvent;
    }
    
    public function pullRecordedEvents(): array
    {
        $events = $this->recordedEvents;
        $this->recordedEvents = [];
        return $events;
    }

}

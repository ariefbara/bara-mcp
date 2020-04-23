<?php

namespace Resources\Application\Event;

interface ContainEvents
{
    /**
     * 
     * @return Event[]
     */
    public function getRecordedEvents();
    public function clearRecordedEvents(): void;
}


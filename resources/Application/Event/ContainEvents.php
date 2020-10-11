<?php

namespace Resources\Application\Event;

interface ContainEvents
{
    /**
     * 
     * @return Event[]
     */
    public function pullRecordedEvents(): array;
}


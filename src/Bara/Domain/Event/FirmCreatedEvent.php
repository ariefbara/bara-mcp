<?php

namespace Bara\Domain\Event;

use Config\EventList;
use Resources\Application\Event\Event;

class FirmCreatedEvent implements Event
{

    protected string $id;
    protected string $identifier;

    public function getId(): string
    {
        return $this->id;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function __construct(string $id, string $identifier)
    {
        $this->id = $id;
        $this->identifier = $identifier;
    }

    public function getName(): string
    {
        return EventList::FIRM_CREATED;
    }
}

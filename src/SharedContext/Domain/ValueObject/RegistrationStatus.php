<?php

namespace SharedContext\Domain\ValueObject;

use Config\EventList;
use Resources\BaseEnum;
use Resources\Exception\RegularException;

class RegistrationStatus extends BaseEnum
{

    const REGISTERED = 1;
    const SETTLEMENT_REQUIRED = 2;
    const ACCEPTED = 3;
    const REJECTED = 4;
    const CANCELLED = 5;

    /**
     * 
     * @var string|null
     */
    protected $emittedEvent;

    public function getEmittedEvent(): ?string
    {
        return $this->emittedEvent;
    }
    
    public function __construct($value)
    {
        parent::__construct($value);
        if ($this->value === self::REGISTERED) {
            $this->emittedEvent = EventList::PROGRAM_REGISTRATION_RECEIVED;
        } elseif ($this->value === self::SETTLEMENT_REQUIRED) {
            $this->emittedEvent = EventList::SETTLEMENT_REQUIRED;
        }
    }

    public function isConcluded(): bool
    {
        return !in_array($this->value, [self::REGISTERED, self::SETTLEMENT_REQUIRED]);
    }

    public function sameValueAs(RegistrationStatus $other): bool
    {
        return $this->value === $other->value;
    }

    public function settle(): self
    {
        if ($this->value !== self::SETTLEMENT_REQUIRED) {
            throw RegularException::forbidden('only registrant with settlement required can settle payment');
        }
        return new static(self::ACCEPTED);
    }

    public function cancel(): self
    {
        if (in_array($this->value, [self::ACCEPTED, self::CANCELLED, self::REJECTED])) {
            throw RegularException::forbidden('registration already concluded');
        }
        return new static(self::CANCELLED);
    }

    public function accept(?int $programPrice): self
    {
        $this->assertRegistered();
        $status = clone $this;
        if (empty($programPrice)) {
            $status->value = self::ACCEPTED;
            $status->emittedEvent = EventList::PROGRAM_PARTICIPATION_ACCEPTED;
        } else {
            $status->value = self::SETTLEMENT_REQUIRED;
            $status->emittedEvent = EventList::SETTLEMENT_REQUIRED;
        }
        return $status;
    }

    public function reject(): self
    {
        $this->assertRegistered();
        $status = clone $this;
        $status->value = self::REJECTED;
        return $status;
    }
    
    protected function assertRegistered(): void
    {
        if ($this->value !== self::REGISTERED) {
            throw RegularException::forbidden('can only accept registered user');
        }
    }

}

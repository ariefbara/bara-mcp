<?php

namespace SharedContext\Domain\ValueObject;

use Resources\BaseEnum;
use Resources\Exception\RegularException;

class MentoringRequestStatus extends BaseEnum
{

    const REQUESTED = 0;
    const OFFERED = 1;
    const CANCELLED = 2;
    const REJECTED = 3;
    const APPROVED_BY_MENTOR = 4;
    const ACCEPTED_BY_PARTICIPANT = 5;
    
    const DISPLAY_VALUE = [
        self::REQUESTED => 'requested by participant',
        self::OFFERED => 'offered by mentor',
        self::CANCELLED => 'cancelled by participant',
        self::REJECTED => 'rejected by mentor',
        self::APPROVED_BY_MENTOR => 'approved by mentor',
        self::ACCEPTED_BY_PARTICIPANT => 'accepted b participant',
    ];

    public function isConcluded(): bool
    {
        return !in_array($this->value, [self::REQUESTED, self::OFFERED]);
    }

    public function cancel(): self
    {
        if ($this->isConcluded()) {
            throw RegularException::forbidden('forbidden: unable to cancel concluded mentoring request');
        }
        $cancelledStatus = clone $this;
        $cancelledStatus->value = self::CANCELLED;
        return $cancelledStatus;
    }

    public function accept(): self
    {
        if ($this->value !== self::OFFERED) {
            throw RegularException::forbidden('forbidden: can only accept offered mentoring request');
        }
        $acceptedStatus = clone $this;
        $acceptedStatus->value = self::ACCEPTED_BY_PARTICIPANT;
        return $acceptedStatus;
    }

    public function isScheduledOrPotentialSchedule(): bool
    {
        return in_array($this->value, [
            self::ACCEPTED_BY_PARTICIPANT,
            self::APPROVED_BY_MENTOR,
            self::REQUESTED,
        ]);
    }
    
    public function getDisplayValue(): string
    {
        return self::DISPLAY_VALUE[$this->value];
    }

}

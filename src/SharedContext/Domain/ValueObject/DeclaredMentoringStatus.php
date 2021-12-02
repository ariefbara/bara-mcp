<?php

namespace SharedContext\Domain\ValueObject;

use Resources\BaseEnum;
use Resources\Exception\RegularException;

class DeclaredMentoringStatus extends BaseEnum
{
    const DECLARED_BY_MENTOR = 1;
    const DECLARED_BY_PARTICIPANT = 2;
    const CANCELLED = 3;
    const APPROVED_BY_MENTOR = 4;
    const DENIED_BY_MENTOR = 5;
    const APPROVED_BY_PARTICIPANT = 6;
    const DENIED_BY_PARTICIPANT = 7;
    
    const DISPLAY_VALUES = [
        self::DECLARED_BY_MENTOR => 'declared by mentor',
        self::DECLARED_BY_PARTICIPANT => 'declared by participant',
        self::CANCELLED => 'cancelled',
        self::APPROVED_BY_MENTOR => 'approved by mentor',
        self::DENIED_BY_MENTOR => 'denied by mentor',
        self::APPROVED_BY_PARTICIPANT => 'approved by participant',
        self::DENIED_BY_PARTICIPANT => 'denied by participant',
    ];
    
    public function getDisplayValue(): string
    {
        return self::DISPLAY_VALUES[$this->value];
    }
    
    public function __construct($value)
    {
        parent::__construct($value);
    }
    
    public function statusEquals(int $value): bool
    {
        return $this->value === $value;
    }
    
    public function cancelMentorDeclaration(): self
    {
        if ($this->value !== self::DECLARED_BY_MENTOR) {
            throw RegularException::forbidden('forbidden: can only cancel declaration in declared by mentor state');
        }
        $cancelledStatus = clone $this;
        $cancelledStatus->value = self::CANCELLED;
        return $cancelledStatus;
    }
    
    public function approveParticipantDeclaration(): self
    {
        if ($this->value !== self::DECLARED_BY_PARTICIPANT) {
            throw RegularException::forbidden('forbidden: can only approve declaration in declared by participant state');
        }
        $approvedStatus = clone $this;
        $approvedStatus->value = self::APPROVED_BY_MENTOR;
        return $approvedStatus;
    }
    
    public function denyParticipantDeclaration(): self
    {
        if ($this->value !== self::DECLARED_BY_PARTICIPANT) {
            throw RegularException::forbidden('forbidden: can only deny declaration in declared by participant state');
        }
        $deniedStatus = clone $this;
        $deniedStatus->value = self::DENIED_BY_MENTOR;
        return $deniedStatus;
    }
    
    public function statusIn(array $declaredStatusList): bool
    {
        return in_array($this->value, $declaredStatusList);
    }
}

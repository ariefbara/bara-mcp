<?php

namespace SharedContext\Domain\ValueObject;

use Resources\BaseEnum;

class RegistrationStatus extends BaseEnum
{
    const REGISTERED = 1;
    const SETTLEMENT_REQUIRED = 2;
    const ACCEPTED = 3;
    const REJECTED = 4;
    const CANCELLED = 5;
    
    public function isConcluded(): bool
    {
        return !in_array($this->value, [self::REGISTERED, self::SETTLEMENT_REQUIRED]);
    }
    
}

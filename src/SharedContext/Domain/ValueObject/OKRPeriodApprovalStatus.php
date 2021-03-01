<?php

namespace SharedContext\Domain\ValueObject;

use Resources\BaseEnum;

class OKRPeriodApprovalStatus extends BaseEnum
{

    const UNCONCLUDED = 0;
    const APPROVED = 1;
    const REJECTED = 2;

    
    public function isConcluded(): bool
    {
        return $this->value !== self::UNCONCLUDED;
    }
    
    public function isRejected(): bool
    {
        return $this->value === self::REJECTED;
    }
    
}

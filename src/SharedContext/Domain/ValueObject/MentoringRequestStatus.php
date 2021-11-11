<?php

namespace SharedContext\Domain\ValueObject;

use Resources\BaseEnum;

class MentoringRequestStatus extends BaseEnum
{
    const REQUESTED = 0;
    const OFFERED = 1;
    const CANCELLED = 2;
    const REJECTED = 3;
    const APPROVED_BY_MENTOR = 4;
    const ACCEPTED_BY_PARTICIPANT = 5;
    
    public function isRequestConcluded(): bool
    {
//        return $this->value !== self::REQUESTED ||$this->value !== self::OFFERED;
    }
}

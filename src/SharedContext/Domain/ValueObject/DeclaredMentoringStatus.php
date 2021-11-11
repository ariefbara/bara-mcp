<?php

namespace SharedContext\Domain\ValueObject;

use Resources\BaseEnum;

class DeclaredMentoringStatus extends BaseEnum
{
    const DECLARED_BY_MENTOR = 0;
    const DECLARED_BY_PARTICIPANT = 1;
    const CANCELLED = 2;
    const APPROVED_BY_MENTOR = 3;
    const DENIED_BY_MENTOR = 4;
    const APPROVED_BY_PARTICIPANT = 5;
    const DENIED_BY_PARTICIPANT = 6;
}

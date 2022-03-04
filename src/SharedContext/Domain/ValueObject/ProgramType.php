<?php

namespace SharedContext\Domain\ValueObject;

use Resources\BaseEnum;

class ProgramType extends BaseEnum
{
    const INCUBATION = 'incubation';
    const COURSE = 'course';
    
    public function getDisplayValue(): string
    {
        switch ($this->value) {
            case self::INCUBATION:
                return 'incubation';
                break;
            default:
                return 'course';
                break;
        }
    }
}

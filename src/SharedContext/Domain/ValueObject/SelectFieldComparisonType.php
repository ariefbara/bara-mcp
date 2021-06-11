<?php

namespace SharedContext\Domain\ValueObject;

use Resources\BaseEnum;

class SelectFieldComparisonType extends BaseEnum
{
    const IN = 1;
    const NOT_IN = 2;
    
    public function getDisplayValue(): string
    {
        switch ($this->value) {
            case 1:
                return 'IN';
            case 2:
                return 'NOT_IN';
            default:
                break;
        }
    }
}

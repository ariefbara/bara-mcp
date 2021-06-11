<?php

namespace SharedContext\Domain\ValueObject;

use Resources\BaseEnum;

class IntegerFieldComparisonType extends BaseEnum
{
    const EQUALS = 1;
    const LESS_THAN = 2;
    const LESS_THAN_OR_EQUALS = 3;
    const GREATER_THAN = 4;
    const GREATER_THAN_OR_EQUALS = 5;
    
    public function getDisplayValue(): string
    {
        switch ($this->value) {
            case 1:
                return 'EQUALS';
            case 2:
                return 'LESS_THAN';
            case 3:
                return 'LESS_THAN_OR_EQUALS';
            case 4:
                return 'GREATER_THAN';
            case 5:
                return 'GREATER_THAN_OR_EQUALS';
            default:
                break;
        }
    }
}

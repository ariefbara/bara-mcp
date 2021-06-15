<?php

namespace SharedContext\Domain\ValueObject;

use Resources\BaseEnum;
use Resources\Exception\RegularException;

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
    
    public function getComparisonQuery(array $listOfOptionId): string
    {
        if (empty($listOfOptionId)) {
            throw RegularException::forbidden('forbidden: option is mandatory to search through select field');
        }
        $values = "";
        foreach ($listOfOptionId as $optionId) {
            $values .= empty($value) ? "'{$optionId}'" : ",'{$optionId}'";
        }
        switch ($this->value) {
            case 1:
                return "IN ($values)";
            case 2:
                return "NOT IN ($values)";
            default:
                break;
        }
    }
}

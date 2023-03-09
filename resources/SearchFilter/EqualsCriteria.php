<?php

namespace Resources\SearchFilter;

class EqualsCriteria extends AbstractCriteria
{

    public function getEvaluationStatement(&$parameters): ?string
    {
        if ($this->value) {
            $parameters[$this->column] = $this->value;
            return "{$this->getTableColumnName()} = :{$this->column}";
        }
        return null;
    }

}

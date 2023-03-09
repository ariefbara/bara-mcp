<?php

namespace Resources;

class SearchFilter
{

    /**
     * 
     * @var SearchCriteriaInterface[]
     */
    protected $criteriaList = [];

    public function __construct()
    {
        
    }

    public function addCriteria(SearchCriteriaInterface $criteria): self
    {
        $this->criteriaList[$criteria->getTableColumnName()] = $criteria;
        return $this;
    }

    public function getEvaluationStatement(&$parameters): ?string
    {
        $statement = '';
        foreach ($this->criteriaList as $criteria) {
            $criteriaStatement = $criteria->getEvaluationStatement($parameters);
            if (!empty($criteriaStatement)) {
                $statement .= <<<_CRITERIA
AND {$criteriaStatement}
_CRITERIA;
            }
        }
        return $statement;
    }

}

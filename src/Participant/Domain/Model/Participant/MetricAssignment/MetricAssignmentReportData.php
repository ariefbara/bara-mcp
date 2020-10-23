<?php

namespace Participant\Domain\Model\Participant\MetricAssignment;

use Resources\Domain\Data\BaseArrayCollection;

class MetricAssignmentReportData
{
    protected $collection;
    
    public function __construct()
    {
        $this->collection = [];
    }

    public function addValueCorrespondWithAssignmentField(?string $assignmentFieldId, ?float $value): void
    {
        $this->collection[$assignmentFieldId] = $value;
    }
    
    public function getValueCorrespondWithAssignmentField(string $assignmentFieldId): ?float
    {
        return isset($this->collection[$assignmentFieldId])? $this->collection[$assignmentFieldId]: null;
    }

}

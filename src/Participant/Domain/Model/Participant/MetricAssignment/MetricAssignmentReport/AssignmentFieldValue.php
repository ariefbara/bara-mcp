<?php

namespace Participant\Domain\Model\Participant\MetricAssignment\MetricAssignmentReport;

use Participant\Domain\Model\Participant\MetricAssignment\{
    AssignmentField,
    MetricAssignmentReport
};

class AssignmentFieldValue
{

    /**
     *
     * @var MetricAssignmentReport
     */
    protected $metricAssignmentReport;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var AssignmentField
     */
    protected $assignmentField;

    /**
     *
     * @var float|null
     */
    protected $value;

    /**
     *
     * @var bool
     */
    protected $removed;

    public function __construct(
            MetricAssignmentReport $metricAssignmentReport, string $id, AssignmentField $assignmentField, ?float $value)
    {
        $this->metricAssignmentReport = $metricAssignmentReport;
        $this->id = $id;
        $this->assignmentField = $assignmentField;
        $this->value = $value;
        $this->removed = false;
    }
    
    public function update(?float $value): void
    {
        $this->value = $value;
    }
    
    public function remove(): void
    {
        $this->removed = true;
    }
    
    public function isNonRemovedAssignmentFieldValueCorrespondWithObsoleteAssignmentField(): bool
    {
        return !$this->removed && $this->assignmentField->isRemoved();
    }
    
    public function isNonRemovedAssignmentFieldValueCorrespondWithAssignmentField(AssignmentField $assignmentField): bool
    {
        return !$this->removed && $this->assignmentField === $assignmentField;
    }

}

<?php

namespace Query\Domain\Model\Firm\Program\Participant\MetricAssignment\MetricAssignmentReport;

use Query\Domain\Model\{
    Firm\Program\Participant\MetricAssignment\AssignmentField,
    Participant\MetricAssignment\MetricAssignmentReport
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

    public function getMetricAssignmentReport(): MetricAssignmentReport
    {
        return $this->metricAssignmentReport;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getAssignmentField(): AssignmentField
    {
        return $this->assignmentField;
    }

    public function getValue(): ?float
    {
        return $this->value;
    }

    public function isRemoved(): bool
    {
        return $this->removed;
    }

    protected function __construct()
    {
        
    }

}

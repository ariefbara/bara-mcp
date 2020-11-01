<?php

namespace Query\Domain\Model\Firm\Program\Participant\MetricAssignment\MetricAssignmentReport;

use Query\Domain\Model\{
    Firm\Program\Participant\MetricAssignment\AssignmentField,
    Firm\Program\Participant\MetricAssignment\MetricAssignmentReport,
    Shared\FileInfo
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
     * @var string|null
     */
    protected $note;

    /**
     *
     * @var FileInfo|null
     */
    protected $attachedFileInfo;

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

    function getNote(): ?string
    {
        return $this->note;
    }

    function getAttachedFileInfo(): ?FileInfo
    {
        return $this->attachedFileInfo;
    }

    public function isRemoved(): bool
    {
        return $this->removed;
    }

    protected function __construct()
    {
        
    }

}

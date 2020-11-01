<?php

namespace Participant\Domain\Model\Participant\MetricAssignment\MetricAssignmentReport;

use Participant\Domain\ {
    Model\Participant\MetricAssignment\AssignmentField,
    Model\Participant\MetricAssignment\MetricAssignmentReport,
    SharedModel\FileInfo
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

    public function __construct(
            MetricAssignmentReport $metricAssignmentReport, string $id, AssignmentField $assignmentField,
            AssignmentFieldValueData $assignmentFieldValueData)
    {
        $this->metricAssignmentReport = $metricAssignmentReport;
        $this->id = $id;
        $this->assignmentField = $assignmentField;
        $this->value = $assignmentFieldValueData->getValue();
        $this->note = $assignmentFieldValueData->getNote();
        $this->attachedFileInfo = $assignmentFieldValueData->getAttachedFileInfo();
        $this->removed = false;
    }

    public function update(AssignmentFieldValueData $assignmentFieldValueData): void
    {
        $this->value = $assignmentFieldValueData->getValue();
        $this->note = $assignmentFieldValueData->getNote();
        $this->attachedFileInfo = $assignmentFieldValueData->getAttachedFileInfo();
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

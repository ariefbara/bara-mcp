<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Participant\MetricAssignment\MetricAssignmentReport;

use Tests\Controllers\RecordPreparation\{
    Firm\Program\Participant\MetricAssignment\RecordOfAssignmentField,
    Firm\Program\Participant\MetricAssignment\RecordOfMetricAssignmentReport,
    Record,
    Shared\RecordOfFileInfo
};

class RecordOfAssignmentFieldValue implements Record
{

    /**
     *
     * @var RecordOfMetricAssignmentReport
     */
    public $metricAssignmentReport;

    /**
     *
     * @var RecordOfAssignmentField
     */
    public $assignmentField;

    /**
     *
     * @var RecordOfFileInfo|null
     */
    public $attachedFileInfo;
    public $id;
    public $inputValue;
    public $note;
    public $removed;

    function __construct(
            RecordOfMetricAssignmentReport $metricAssignmentReport, RecordOfAssignmentField $assignmentField, $index,
            ?RecordOfFileInfo $attachedFileInfo = null)
    {
        $this->metricAssignmentReport = $metricAssignmentReport;
        $this->assignmentField = $assignmentField;
        $this->attachedFileInfo = $attachedFileInfo;
        $this->id = "assignmentFieldValue-$index-id";
        $this->inputValue = 999.99;
        $this->note = "assignment file value $index note";
        $this->removed = false;
    }

    public function toArrayForDbEntry()
    {
        return [
            "MetricAssignmentReport_id" => $this->metricAssignmentReport->id,
            "AssignmentField_id" => $this->assignmentField->id,
            "id" => $this->id,
            "inputValue" => $this->inputValue,
            "note" => $this->note,
            "FileInfo_idOfAttachment" => isset($this->attachedFileInfo) ? $this->attachedFileInfo->id : null,
            "removed" => $this->removed,
        ];
    }

}

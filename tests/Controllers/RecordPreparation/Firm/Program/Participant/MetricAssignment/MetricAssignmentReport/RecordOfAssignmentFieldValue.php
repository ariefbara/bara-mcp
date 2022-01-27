<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Participant\MetricAssignment\MetricAssignmentReport;

use Illuminate\Database\ConnectionInterface;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\MetricAssignment\RecordOfAssignmentField;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\MetricAssignment\RecordOfMetricAssignmentReport;
use Tests\Controllers\RecordPreparation\Record;
use Tests\Controllers\RecordPreparation\Shared\RecordOfFileInfo;

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
        $this->inputValue = intval($index);
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
    
    public function insert(ConnectionInterface $connection): void
    {
        $connection->table('AssignmentFieldValue')->insert($this->toArrayForDbEntry());
    }

}

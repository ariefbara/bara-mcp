<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Participant\MetricAssignment\MetricAssignmentReport;

use Tests\Controllers\RecordPreparation\{
    Firm\Program\Participant\MetricAssignment\RecordOfAssignmentField,
    Firm\Program\Participant\MetricAssignment\RecordOfMetricAssignmentReport,
    Record
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
    public $id;
    public $inputValue;
    public $removed;

    public function __construct(
            RecordOfMetricAssignmentReport $metricAssignmentReport, RecordOfAssignmentField $assignmentField, $index)
    {
        $this->metricAssignmentReport = $metricAssignmentReport;
        $this->assignmentField = $assignmentField;
        $this->id = "assignmentFieldValue-$index-id";
        $this->inputValue = 999.99;
        $this->removed = false;
    }

    public function toArrayForDbEntry()
    {
        return [
            "MetricAssignmentReport_id" => $this->metricAssignmentReport->id,
            "AssignmentField_id" => $this->assignmentField->id,
            "id" => $this->id,
            "inputValue" => $this->inputValue,
            "removed" => $this->removed,
        ];
    }

}

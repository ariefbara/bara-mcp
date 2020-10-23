<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Participant\MetricAssignment;

use Tests\Controllers\RecordPreparation\{
    Firm\Program\Participant\RecordOfMetricAssignment,
    Firm\Program\RecordOfMetric,
    Record
};

class RecordOfAssignmentField implements Record
{

    /**
     *
     * @var RecordOfMetricAssignment
     */
    public $metricAssignment;

    /**
     *
     * @var RecordOfMetric
     */
    public $metric;
    public $id;
    public $target;
    public $removed;

    public function __construct(RecordOfMetricAssignment $metricAssignment, RecordOfMetric $metric, $index)
    {
        $this->metricAssignment = $metricAssignment;
        $this->metric = $metric;
        $this->id = "assignmentField-$index-id";
        $this->target = 999;
        $this->removed = false;
    }

    public function toArrayForDbEntry()
    {
        return [
            "MetricAssignment_id" => $this->metricAssignment->id,
            "Metric_id" => $this->metric->id,
            "id" => $this->id,
            "target" => $this->target,
            "removed" => $this->removed,
        ];
    }

}

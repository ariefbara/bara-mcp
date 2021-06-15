<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Participant\MetricAssignment;

use Illuminate\Database\ConnectionInterface;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfMetricAssignment;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfMetric;
use Tests\Controllers\RecordPreparation\Record;

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
    public $disabled;

    public function __construct(RecordOfMetricAssignment $metricAssignment, RecordOfMetric $metric, $index)
    {
        $this->metricAssignment = $metricAssignment;
        $this->metric = $metric;
        $this->id = "assignmentField-$index-id";
        $this->target = 999;
        $this->disabled = false;
    }

    public function toArrayForDbEntry()
    {
        return [
            "MetricAssignment_id" => $this->metricAssignment->id,
            "Metric_id" => $this->metric->id,
            "id" => $this->id,
            "target" => $this->target,
            "disabled" => $this->disabled,
        ];
    }
    
    public function insert(ConnectionInterface $connection): void
    {
        $connection->table('AssignmentField')->insert($this->toArrayForDbEntry());
    }

}

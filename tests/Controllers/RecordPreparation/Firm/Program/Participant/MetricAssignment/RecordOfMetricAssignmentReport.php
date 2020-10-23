<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Participant\MetricAssignment;

use Tests\Controllers\RecordPreparation\ {
    Firm\Program\Participant\RecordOfMetricAssignment,
    Record
};

class RecordOfMetricAssignmentReport implements Record
{
    /**
     *
     * @var RecordOfMetricAssignment
     */
    public $metricAssignment;
    public $id;
    public $observeTime;
    public $submitTime;
    public $removed;
    
    public function __construct(RecordOfMetricAssignment $metricAssignment, $index)
    {
        $this->metricAssignment = $metricAssignment;
        $this->id = "metricAssignmentReport-$index-id";
        $this->observeTime = (new \DateTimeImmutable("-1 days"))->format("Y-m-d H:i:s");
        $this->submitTime = (new \DateTimeImmutable())->format("Y-m-d H:i:s");
        $this->removed = false;
    }

    public function toArrayForDbEntry()
    {
        return [
            "MetricAssignment_id" => $this->metricAssignment->id,
            "id" => $this->id,
            "observeTime" => $this->observeTime,
            "submitTime" => $this->submitTime,
            "removed" => $this->removed,
        ];
    }

}

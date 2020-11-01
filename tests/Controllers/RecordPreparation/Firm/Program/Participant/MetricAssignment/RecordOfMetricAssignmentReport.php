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
    public $observationTime;
    public $submitTime;
    public $removed;
    
    public function __construct(RecordOfMetricAssignment $metricAssignment, $index)
    {
        $this->metricAssignment = $metricAssignment;
        $this->id = "metricAssignmentReport-$index-id";
        $this->observationTime = (new \DateTimeImmutable("-1 days"))->format("Y-m-d H:i:s");
        $this->submitTime = (new \DateTimeImmutable())->format("Y-m-d H:i:s");
        $this->removed = false;
    }

    public function toArrayForDbEntry()
    {
        return [
            "MetricAssignment_id" => $this->metricAssignment->id,
            "id" => $this->id,
            "observationTime" => $this->observationTime,
            "submitTime" => $this->submitTime,
            "removed" => $this->removed,
        ];
    }

}

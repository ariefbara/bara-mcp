<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Participant\MetricAssignment;

use DateTimeImmutable;
use Illuminate\Database\ConnectionInterface;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfMetricAssignment;
use Tests\Controllers\RecordPreparation\Record;

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
    public $approved;
    public $note;
    public $removed;
    
    public function __construct(RecordOfMetricAssignment $metricAssignment, $index)
    {
        $this->metricAssignment = $metricAssignment;
        $this->id = "metricAssignmentReport-$index-id";
        $this->observationTime = (new DateTimeImmutable("-1 days"))->format("Y-m-d H:i:s");
        $this->submitTime = (new DateTimeImmutable())->format("Y-m-d H:i:s");
        $this->approved = null;
        $this->note = null;
        $this->removed = false;
    }

    public function toArrayForDbEntry()
    {
        return [
            "MetricAssignment_id" => $this->metricAssignment->id,
            "id" => $this->id,
            "observationTime" => $this->observationTime,
            "submitTime" => $this->submitTime,
            "approved" => $this->approved,
            "note" => $this->note,
            "removed" => $this->removed,
        ];
    }
    
    public function insert(ConnectionInterface $connection): void
    {
        $connection->table('MetricAssignmentReport')->insert($this->toArrayForDbEntry());
    }

}

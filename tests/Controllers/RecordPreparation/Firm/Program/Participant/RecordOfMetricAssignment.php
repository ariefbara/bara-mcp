<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Participant;

use DateTimeImmutable;
use Tests\Controllers\RecordPreparation\ {
    Firm\Program\RecordOfParticipant,
    Record
};

class RecordOfMetricAssignment implements Record
{
    /**
     *
     * @var RecordOfParticipant
     */
    public $participant;
    public $id;
    public $startDate;
    public $endDate;
    
    public function __construct(RecordOfParticipant $participant, $index)
    {
        $this->participant = $participant;
        $this->id = "metricAssignment-$index-id";
        $this->startDate = (new DateTimeImmutable("+1 months"))->format("Y-m-d H:i:s");
        $this->endDate = (new DateTimeImmutable("+12 months"))->format("Y-m-d H:i:s");
    }
    
    public function toArrayForDbEntry()
    {
        return [
            "Participant_id" => $this->participant->id,
            "id" => $this->id,
            "startDate" => $this->startDate,
            "endDate" => $this->endDate,
        ];
    }

}

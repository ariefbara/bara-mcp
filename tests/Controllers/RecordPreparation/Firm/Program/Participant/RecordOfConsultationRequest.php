<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Participant;

use DateTime;
use Tests\Controllers\RecordPreparation\ {
    Firm\Program\RecordOfConsultant,
    Firm\Program\RecordOfConsultationSetup,
    Firm\Program\RecordOfParticipant,
    Record
};

class RecordOfConsultationRequest implements Record
{
    /**
     *
     * @var RecordOfConsultationSetup
     */
    public $consultationSetup;
    /**
     *
     * @var RecordOfParticipant
     */
    public $participant;
    /**
     *
     * @var RecordOfConsultant
     */
    public $consultant;
    public $id, $startDateTime, $endDateTime, $concluded = false, $status;
    
    function __construct(
            RecordOfConsultationSetup $consultationSetup, RecordOfParticipant $participant, RecordOfConsultant $consultant, $index)
    {
        $this->consultationSetup = $consultationSetup;
        $this->participant = $participant;
        $this->consultant = $consultant;
        $this->id = "negotiate-schedule-$index-id";
        $this->startDateTime = (new DateTime("+24 hours"))->format('Y-m-d H:i:s');
        $this->endDateTime = (new DateTime("+25 hours"))->format('Y-m-d H:i:s');
        $this->concluded = false;
        $this->status = "proposed";
    }

    
    public function toArrayForDbEntry()
    {
        return [
            "ConsultationSetup_id" => $this->consultationSetup->id,
            "Participant_id" => $this->participant->id,
            "Consultant_id" => $this->consultant->id,
            "id" => $this->id,
            "startDateTime" => $this->startDateTime,
            "endDateTime" => $this->endDateTime,
            "concluded" => $this->concluded,
            "status" => $this->status,
        ];
    }

}

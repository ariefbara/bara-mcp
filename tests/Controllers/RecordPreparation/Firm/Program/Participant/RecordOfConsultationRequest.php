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
    public $media, $address;
    
    function __construct(
            ?RecordOfConsultationSetup $consultationSetup, ?RecordOfParticipant $participant, ?RecordOfConsultant $consultant, $index)
    {
        $this->consultationSetup = $consultationSetup;
        $this->participant = $participant;
        $this->consultant = $consultant;
        $this->id = "consultation-request-$index-id";
        $this->startDateTime = (new DateTime("+128 hours"))->format('Y-m-d H:i:s');
        $this->endDateTime = (new DateTime("+129 hours"))->format('Y-m-d H:i:s');
        $this->media = "media $index";
        $this->address = "consultation request $index address";
        $this->concluded = false;
        $this->status = "proposed";
    }

    
    public function toArrayForDbEntry()
    {
        return [
            "ConsultationSetup_id" => isset($this->consultationSetup)? $this->consultationSetup->id: null,
            "Participant_id" => isset($this->participant)? $this->participant->id: null,
            "Consultant_id" => isset($this->consultant)? $this->consultant->id: null,
            "id" => $this->id,
            "startDateTime" => $this->startDateTime,
            "endDateTime" => $this->endDateTime,
            "media" => $this->media,
            "address" => $this->address,
            "concluded" => $this->concluded,
            "status" => $this->status,
        ];
    }

}

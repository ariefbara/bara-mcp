<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Participant;

use DateTime;
use Illuminate\Database\ConnectionInterface;
use SharedContext\Domain\ValueObject\ConsultationSessionType;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultationSetup;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Record;

class RecordOfConsultationSession implements Record
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
    public $id, $startDateTime, $endDateTime;
    public $cancelled;
    public $note;
    public $media;
    public $address;
    public $sessionType;
    public $approvedByMentor;
    
    function __construct(
            ?RecordOfConsultationSetup $consultationSetup, ?RecordOfParticipant $participant, ?RecordOfConsultant $consultant, $index)
    {
        $this->consultationSetup = $consultationSetup;
        $this->participant = $participant;
        $this->consultant = $consultant;
        $this->id = "schedule-$index-id";
        $this->startDateTime = (new DateTime("+96 hours"))->format('Y-m-d H:i:s');
        $this->endDateTime = (new DateTime("+97 hours"))->format('Y-m-d H:i:s');
        $this->cancelled = false;
        $this->note = null;
        $this->media = "media $index";
        $this->address = "consultation request $index address";
        $this->sessionType = ConsultationSessionType::HANDSHAKING_TYPE;
        $this->approvedByMentor = null;
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
            "cancelled" => $this->cancelled,
            "note" => $this->note,
            "media" => $this->media,
            "address" => $this->address,
            "sessionType" => $this->sessionType,
            'approvedByMentor' => $this->approvedByMentor,
        ];
    }
    
    public function insert(ConnectionInterface $connection)
    {
        $connection->table('ConsultationSession')->insert($this->toArrayForDbEntry());
    }

}

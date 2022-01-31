<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Participant;

use DateTimeImmutable;
use Illuminate\Database\ConnectionInterface;
use SharedContext\Domain\ValueObject\MentoringRequestStatus;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultationSetup;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Record;

class RecordOfMentoringRequest implements Record
{

    /**
     * 
     * @var RecordOfParticipant
     */
    public $participant;

    /**
     * 
     * @var RecordOfConsultant
     */
    public $mentor;

    /**
     * 
     * @var RecordOfConsultationSetup
     */
    public $consultationSetup;
    public $id;
    public $startTime;
    public $endTime;
    public $mediaType;
    public $location;
    public $requestStatus;

    public function __construct(
            RecordOfParticipant $participant, RecordOfConsultant $mentor, RecordOfConsultationSetup $consultationSetup,
            $index)
    {
        $this->participant = $participant;
        $this->mentor = $mentor;
        $this->consultationSetup = $consultationSetup;
        $this->id = "mentoringRequest-$index-id";
        $this->startTime = (new DateTimeImmutable('+24 hours'))->format('Y-m-d H:i:s');
        $this->endTime = (new DateTimeImmutable('+25 hours'))->format('Y-m-d H:i:s');
        $this->mediaType = "media type $index";
        $this->location = "location $index";
        $this->requestStatus = MentoringRequestStatus::REQUESTED;
    }

    public function toArrayForDbEntry()
    {
        return [
            'Participant_id' => $this->participant->id,
            'Consultant_id' => $this->mentor->id,
            'ConsultationSetup_id' => $this->consultationSetup->id,
            'id' => $this->id,
            'startTime' => $this->startTime,
            'endTime' => $this->endTime,
            'mediaType' => $this->mediaType,
            'location' => $this->location,
            'requestStatus' => $this->requestStatus,
        ];
    }
    
    public function insert(ConnectionInterface $connection)
    {
        $connection->table('MentoringRequest')->insert($this->toArrayForDbEntry());
    }

}

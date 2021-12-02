<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Participant;

use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultationSetup;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Record;
use Tests\Controllers\RecordPreparation\Shared\RecordOfMentoring;

class RecordOfDeclaredMentoring implements Record
{
    /**
     * 
     * @var RecordOfConsultant
     */
    public $mentor;
    /**
     * 
     * @var RecordOfParticipant
     */
    public $participant;
    /**
     * 
     * @var RecordOfConsultationSetup
     */
    public $consultationSetup;
    /**
     * 
     * @var RecordOfMentoring
     */
    public $mentoring;
    public $id;
    public $declaredStatus;
    public $startTime;
    public $endTime;
    public $mediaType;
    public $location;
    
    public function __construct(
            RecordOfConsultant $mentor, RecordOfParticipant $participant,
            RecordOfConsultationSetup $consultationSetup, RecordOfMentoring $mentoring)
    {
        $this->mentor = $mentor;
        $this->participant = $participant;
        $this->consultationSetup = $consultationSetup;
        $this->mentoring = $mentoring;
        $this->id = $mentoring->id;
        $this->declaredStatus = \SharedContext\Domain\ValueObject\DeclaredMentoringStatus::DECLARED_BY_MENTOR;
        $this->startTime = (new \DateTimeImmutable('-25 hours'))->format('Y-m-d H:i:s');
        $this->endTime = (new \DateTimeImmutable('-24 hours'))->format('Y-m-d H:i:s');
        $this->mediaType = "declared mentoring $this->id media type";
        $this->location = "declared mentoring $this->id location";
    }

    
    public function toArrayForDbEntry()
    {
        return [
            'Consultant_id' => $this->mentor->id,
            'Participant_id' => $this->participant->id,
            'ConsultationSetup_id' => $this->consultationSetup->id,
            'Mentoring_id' => $this->mentoring->id,
            'id' => $this->id,
            'declaredStatus' => $this->declaredStatus,
            'startTime' => $this->startTime,
            'endTime' => $this->endTime,
            'mediaType' => $this->mediaType,
            'location' => $this->location,
        ];
    }
    
    public function insert(\Illuminate\Database\ConnectionInterface $connection): void
    {
        $this->mentoring->insert($connection);
        $connection->table('DeclaredMentoring')->insert($this->toArrayForDbEntry());
    }

}

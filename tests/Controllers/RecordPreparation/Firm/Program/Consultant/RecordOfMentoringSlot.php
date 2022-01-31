<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Consultant;

use DateTimeImmutable;
use Illuminate\Database\ConnectionInterface;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultationSetup;
use Tests\Controllers\RecordPreparation\Record;

class RecordOfMentoringSlot implements Record
{

    /**
     * 
     * @var RecordOfConsultant
     */
    public $consultant;

    /**
     * 
     * @var RecordOfConsultationSetup
     */
    public $consultationSetup;
    public $id;
    public $cancelled;
    /**
     * 
     * @var DateTimeImmutable
     */
    public $startTime;
    /**
     * 
     * @var DateTimeImmutable
     */
    public $endTime;
    public $mediaType;
    public $location;
    public $capacity;

    public function __construct(RecordOfConsultant $consultant, RecordOfConsultationSetup $consultationSetup, $index)
    {
        $this->consultant = $consultant;
        $this->consultationSetup = $consultationSetup;
        $this->id = "mentoringSlot-$index-id";
        $this->cancelled = false;
        $this->startTime = (new DateTimeImmutable('+24 hours'));
        $this->endTime = (new DateTimeImmutable('+25 hours'));
        $this->mediaType = 'online';
        $this->location = 'meet.google.com';
        $this->capacity = 3;
    }

    public function toArrayForDbEntry()
    {
        return [
            'id' => $this->id,
            'cancelled' => $this->cancelled,
            'startTime' => $this->startTime->format('Y-m-d H:i:s'),
            'endTime' => $this->endTime->format('Y-m-d H:i:s'),
            'mediaType' => $this->mediaType,
            'location' => $this->location,
            'capacity' => $this->capacity,
            'Mentor_id' => $this->consultant->id,
            'ConsultationSetup_id' => $this->consultationSetup->id,
        ];
    }

    public function insert(ConnectionInterface $connection): void
    {
        $connection->table('MentoringSlot')->insert($this->toArrayForDbEntry());
    }

}

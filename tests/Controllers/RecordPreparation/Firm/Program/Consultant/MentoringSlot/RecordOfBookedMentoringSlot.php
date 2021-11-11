<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Consultant\MentoringSlot;

use Illuminate\Database\ConnectionInterface;
use Tests\Controllers\RecordPreparation\Firm\Program\Consultant\RecordOfMentoringSlot;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Record;
use Tests\Controllers\RecordPreparation\Shared\RecordOfMentoring;

class RecordOfBookedMentoringSlot implements Record
{

    /**
     * 
     * @var RecordOfMentoringSlot
     */
    public $mentoringSlot;

    /**
     * 
     * @var RecordOfMentoring
     */
    public $mentoring;

    /**
     * 
     * @var RecordOfParticipant
     */
    public $participant;
    public $cancelled;

    public function __construct(RecordOfMentoringSlot $mentoringSlot, RecordOfMentoring $mentoring,
            RecordOfParticipant $participant)
    {
        $this->mentoringSlot = $mentoringSlot;
        $this->mentoring = $mentoring;
        $this->participant = $participant;
        $this->cancelled = false;
    }

    public function toArrayForDbEntry()
    {
        return [
            'id' => $this->mentoring->id,
            'cancelled' => $this->cancelled,
            'MentoringSlot_id' => $this->mentoringSlot->id,
            'Participant_id' => $this->participant->id,
            'Mentoring_id' => $this->mentoring->id,
        ];
    }

    public function insert(ConnectionInterface $connection): void
    {
        $this->mentoring->insert($connection);
        $connection->table('BookedMentoringSlot')->insert($this->toArrayForDbEntry());
    }

}

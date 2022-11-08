<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Consultant;

use Illuminate\Database\ConnectionInterface;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Record;
use Tests\Controllers\RecordPreparation\Shared\RecordOfNote;

class RecordOfConsultantNote implements Record
{

    /**
     * 
     * @var RecordOfNote
     */
    public $note;

    /**
     * 
     * @var RecordOfConsultant
     */
    public $consultant;

    /**
     * 
     * @var RecordOfParticipant
     */
    public $participant;
    public $viewableByParticipant = false;
    public $id;

    public function __construct(
            RecordOfNote $note, RecordOfConsultant $consultant, RecordOfParticipant $participant)
    {
        $this->note = $note;
        $this->consultant = $consultant;
        $this->participant = $participant;
        $this->id = $note->id;
    }

    public function toArrayForDbEntry()
    {
        return [
            'id' => $this->id,
            'viewableByParticipant' => $this->viewableByParticipant,
            'Note_id' => $this->note->id,
            'Consultant_id' => $this->consultant->id,
            'Participant_id' => $this->participant->id,
        ];
    }
    
    public function insert(ConnectionInterface $connection): void
    {
        $this->note->insert($connection);
        $connection->table('ConsultantNote')->insert($this->toArrayForDbEntry());
    }

}

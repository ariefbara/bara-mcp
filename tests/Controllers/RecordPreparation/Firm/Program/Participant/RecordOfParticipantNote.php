<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Participant;

use Illuminate\Database\ConnectionInterface;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Record;
use Tests\Controllers\RecordPreparation\Shared\RecordOfNote;

class RecordOfParticipantNote implements Record
{

    /**
     * 
     * @var RecordOfNote
     */
    public $note;

    /**
     * 
     * @var RecordOfParticipant
     */
    public $participant;
    public $id;

    public function __construct(RecordOfNote $note, RecordOfParticipant $participant)
    {
        $this->note = $note;
        $this->participant = $participant;
        $this->id = $note->id;
    }

    public function toArrayForDbEntry()
    {
        return [
            'Note_id' => $this->note->id,
            'Participant_id' => $this->participant->id,
            'id' => $this->id,
        ];
    }

    public function insert(ConnectionInterface $connection): void
    {
        $this->note->insert($connection);
        $connection->table('ParticipantNote')->insert($this->toArrayForDbEntry());
    }

}

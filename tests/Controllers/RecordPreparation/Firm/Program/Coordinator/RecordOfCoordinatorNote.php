<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Coordinator;

use Illuminate\Database\ConnectionInterface;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfCoordinator;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Record;
use Tests\Controllers\RecordPreparation\Shared\RecordOfNote;

class RecordOfCoordinatorNote implements Record
{

    /**
     * 
     * @var RecordOfCoordinator
     */
    public $coordinator;

    /**
     * 
     * @var RecordOfParticipant
     */
    public $participant;

    /**
     * 
     * @var RecordOfNote
     */
    public $note;
    public $id;
    public $viewableByParticipant = true;

    public function __construct(RecordOfNote $note, RecordOfCoordinator $coordinator, RecordOfParticipant $participant)
    {
        $this->coordinator = $coordinator;
        $this->participant = $participant;
        $this->note = $note;
        $this->id = $note->id;
        $this->viewableByParticipant = true;
    }

    public function toArrayForDbEntry()
    {
        return [
            'id' => $this->id,
            'viewableByParticipant' => $this->viewableByParticipant,
            'Coordinator_id' => $this->coordinator->id,
            'Participant_id' => $this->participant->id,
            'Note_id' => $this->note->id,
        ];
    }
    
    public function insert(ConnectionInterface $connection): void
    {
        $this->note->insert($connection);
        $connection->table('CoordinatorNote')->insert($this->toArrayForDbEntry());
    }

}
